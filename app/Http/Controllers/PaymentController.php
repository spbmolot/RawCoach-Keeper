<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\Coupon;
use App\Services\SubscriptionService;
use App\Services\Payments\YooKassaService;
use App\Services\Payments\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\PaymentProcessed;
use App\Events\SubscriptionStatusChanged;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['webhook']);
    }

    /**
     * Показать страницу обработки платежа
     */
    public function process(Payment $payment)
    {
        $this->authorize('view', $payment);

        if ($payment->status !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'Этот платеж уже обработан');
        }

        // Генерируем данные для платежной формы
        $paymentData = $this->preparePaymentData($payment);

        return view('payments.process', compact('payment', 'paymentData'));
    }

    /**
     * Создание платежа в платежной системе
     */
    public function create(Request $request, Payment $payment)
    {
        $this->authorize('view', $payment);

        $request->validate([
            'provider' => 'required|in:yookassa,cloudpayments',
        ]);

        if ($payment->status !== 'pending') {
            return response()->json(['error' => 'Платеж уже обработан'], 400);
        }

        try {
            $provider = $request->provider;
            $payment->update(['provider' => $provider]);

            // В зависимости от провайдера создаем платеж
            if ($provider === 'yookassa') {
                $result = $this->createYooKassaPayment($payment);
            } else {
                $result = $this->createCloudPaymentsPayment($payment);
            }

            $payment->update([
                'external_id' => $result['payment_id'],
                'payment_url' => $result['payment_url'] ?? null,
                'payload' => $result,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'payment_id' => $result['payment_id'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка создания платежа: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Страница успешного платежа
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if (!$paymentId) {
            return redirect()->route('dashboard')
                ->with('error', 'Не указан ID платежа');
        }

        $payment = Payment::where('external_id', $paymentId)->first();
        
        if (!$payment || $payment->user_id !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Платеж не найден');
        }

        return view('payments.success', compact('payment'));
    }

    /**
     * Страница отмененного платежа
     */
    public function cancel(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if ($paymentId) {
            $payment = Payment::where('external_id', $paymentId)->first();
            if ($payment && $payment->user_id === auth()->id()) {
                $payment->update(['status' => 'cancelled']);
            }
        }

        return view('payments.cancel');
    }

    /**
     * Обработка вебхука от платежной системы
     */
    public function webhook(Request $request, string $provider)
    {
        if (!in_array($provider, ['yookassa', 'cloudpayments'])) {
            abort(404);
        }

        try {
            if ($provider === 'yookassa') {
                $this->handleYooKassaWebhook($request);
            } else {
                $this->handleCloudPaymentsWebhook($request);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            \Log::error("Webhook error ({$provider}): " . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * История платежей пользователя
     */
    public function history()
    {
        $payments = auth()->user()->payments()
            ->with(['plan', 'subscription', 'coupon'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('payments.history', compact('payments'));
    }

    /**
     * Детали конкретного платежа
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return view('payments.show', compact('payment'));
    }

    /**
     * Подготовка данных для платежа
     */
    private function preparePaymentData(Payment $payment): array
    {
        $provider = $payment->provider ?: config('payments.default_provider', 'yookassa');
        $webhookUrl = $provider === 'cloudpayments' ? route('webhook.cloudpayments') : route('webhook.yookassa');

        return [
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'description' => $payment->description,
            'return_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
            'webhook_url' => $webhookUrl,
        ];
    }

    /**
     * Создание платежа в YooKassa
     */
    private function createYooKassaPayment(Payment $payment): array
    {
        $yooKassaService = app(YooKassaService::class);

        $response = $yooKassaService->createPayment([
            'user_id' => $payment->user_id,
            'subscription_id' => $payment->subscription_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'RUB',
            'description' => $payment->description,
            'return_url' => route('payment.success', ['payment_id' => $payment->external_id]),
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
            ],
        ]);

        return [
            'payment_id' => $response->external_id,
            'payment_url' => $response->payload['confirmation_url'] ?? route('payment.success'),
            'status' => $response->status,
        ];
    }

    /**
     * Создание платежа в CloudPayments
     */
    private function createCloudPaymentsPayment(Payment $payment): array
    {
        $cloudPaymentsService = app(CloudPaymentsService::class);

        $response = $cloudPaymentsService->createPayment([
            'user_id' => $payment->user_id,
            'subscription_id' => $payment->subscription_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'RUB',
            'description' => $payment->description,
            'email' => $payment->user->email ?? null,
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
            ],
        ]);

        return [
            'payment_id' => $response->external_id,
            'payment_url' => $response->payload['PaymentUrl'] ?? route('payment.success'),
            'status' => $response->status,
        ];
    }

    /**
     * Обработка вебхука YooKassa
     */
    private function handleYooKassaWebhook(Request $request): void
    {
        $data = $request->all();
        $paymentId = $data['object']['id'] ?? null;
        $status = $data['object']['status'] ?? null;

        if (!$paymentId) {
            throw new \Exception('Payment ID not found in webhook');
        }

        $payment = Payment::where('external_id', $paymentId)->first();
        if (!$payment) {
            throw new \Exception('Payment not found: ' . $paymentId);
        }
        // Map YooKassa statuses to internal
        $mapped = match($status) {
            'succeeded' => 'paid',
            'canceled' => 'cancelled',
            default => 'pending'
        };

        $this->updatePaymentStatus($payment, $mapped, $data);
    }

    /**
     * Обработка вебхука CloudPayments
     */
    private function handleCloudPaymentsWebhook(Request $request): void
    {
        $data = $request->all();
        $paymentId = $data['TransactionId'] ?? null;
        $status = $data['Status'] ?? null;

        if (!$paymentId) {
            throw new \Exception('Transaction ID not found in webhook');
        }

        $payment = Payment::where('external_id', $paymentId)->first();
        if (!$payment) {
            throw new \Exception('Payment not found: ' . $paymentId);
        }

        // Конвертируем статус CloudPayments в наш формат
        $mappedStatus = $this->mapCloudPaymentsStatus($status);
        $this->updatePaymentStatus($payment, $mappedStatus, $data);
    }

    /**
     * Обновление статуса платежа
     */
    private function updatePaymentStatus(Payment $payment, string $status, array $webhookData): void
    {
        $subscriptionService = app(SubscriptionService::class);
        
        $payment->update([
            'status' => $status,
            'webhook_payload' => $webhookData,
            'processed_at' => Carbon::now(),
        ]);

        // Если платеж успешен, активируем подписку через единый сервис
        if ($status === 'paid' && $payment->subscription_id) {
            $subscriptionService->activateSubscription($payment);
        }

        // Если платеж отклонен, деактивируем подписку через единый сервис
        if ($status === 'failed' && $payment->subscription_id) {
            $subscriptionService->deactivateSubscription($payment, 'Платеж отклонен');
        }

        // Dispatch payment event
        event(new PaymentProcessed($payment, $status));
    }

    /**
     * Маппинг статусов CloudPayments
     */
    private function mapCloudPaymentsStatus(string $status): string
    {
        return match($status) {
            'Completed' => 'paid',
            'Authorized' => 'pending',
            'Declined' => 'failed',
            'Cancelled' => 'cancelled',
            default => 'pending'
        };
    }
}
