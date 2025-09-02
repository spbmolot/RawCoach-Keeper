<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                'payment_data' => $result,
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
        return [
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'description' => $payment->description,
            'return_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
            'webhook_url' => route('payment.webhook', ['provider' => 'yookassa']),
        ];
    }

    /**
     * Создание платежа в YooKassa
     */
    private function createYooKassaPayment(Payment $payment): array
    {
        // Здесь будет интеграция с YooKassa API
        // Пока заглушка
        return [
            'payment_id' => 'yk_' . Str::uuid(),
            'payment_url' => 'https://yoomoney.ru/checkout/payments/v2/contract',
            'status' => 'pending'
        ];
    }

    /**
     * Создание платежа в CloudPayments
     */
    private function createCloudPaymentsPayment(Payment $payment): array
    {
        // Здесь будет интеграция с CloudPayments API
        // Пока заглушка
        return [
            'payment_id' => 'cp_' . Str::uuid(),
            'payment_url' => 'https://widget.cloudpayments.ru/widgets/payment',
            'status' => 'pending'
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

        $this->updatePaymentStatus($payment, $status, $data);
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
        DB::beginTransaction();

        try {
            $payment->update([
                'status' => $status,
                'webhook_data' => $webhookData,
                'processed_at' => Carbon::now(),
            ]);

            // Если платеж успешен, активируем подписку
            if ($status === 'completed' && $payment->subscription) {
                $payment->subscription->update(['status' => 'active']);

                // Если использовался купон, увеличиваем счетчик использований
                if ($payment->coupon) {
                    $payment->coupon->increment('used_count');
                    
                    // Создаем запись об использовании купона
                    $payment->coupon->couponUsages()->create([
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                        'used_at' => Carbon::now(),
                    ]);
                }
            }

            // Если платеж отклонен, отменяем подписку
            if ($status === 'failed' && $payment->subscription) {
                $payment->subscription->update(['status' => 'cancelled']);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Маппинг статусов CloudPayments
     */
    private function mapCloudPaymentsStatus(string $status): string
    {
        return match($status) {
            'Completed' => 'completed',
            'Authorized' => 'pending',
            'Declined' => 'failed',
            'Cancelled' => 'cancelled',
            default => 'pending'
        };
    }
}
