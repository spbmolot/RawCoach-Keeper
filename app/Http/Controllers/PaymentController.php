<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\Coupon;
use App\Services\Payments\YooKassaService;
use App\Services\Payments\CloudPaymentsService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

            Log::channel('payments')->info('Payment creation initiated', [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'provider' => $provider,
                'amount' => $payment->amount,
                'ip' => request()->ip(),
            ]);

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

            Log::channel('payments')->info('Payment created in provider', [
                'payment_id' => $payment->id,
                'external_id' => $result['payment_id'],
                'provider' => $provider,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'payment_id' => $result['payment_id'],
            ]);

        } catch (\Exception $e) {
            Log::channel('payments')->error('Payment creation failed', [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'provider' => $request->provider,
                'error' => $e->getMessage(),
            ]);
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
            Log::channel('payments')->warning('Payment success page: payment not found or unauthorized', [
                'external_id' => $paymentId,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Платеж не найден');
        }

        Log::channel('payments')->info('Payment success page visited', [
            'payment_id' => $payment->id,
            'user_id' => auth()->id(),
            'status' => $payment->status,
            'amount' => $payment->amount,
        ]);

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
                Log::channel('payments')->info('Payment cancelled by user', [
                    'payment_id' => $payment->id,
                    'user_id' => auth()->id(),
                    'amount' => $payment->amount,
                    'ip' => request()->ip(),
                ]);
            }
        }

        return view('payments.cancel');
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

}
