<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class TestPaymentController extends Controller
{
    /**
     * Страница тестового подтверждения платежа
     * Используется только в режиме разработки
     */
    public function confirm(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }
        
        $paymentId = $request->get('payment_id');
        $payment = Payment::where('external_id', $paymentId)->first();
        
        return view('payments.test-confirm', compact('payment', 'paymentId'));
    }
    
    /**
     * Симуляция успешной оплаты
     */
    public function success(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }
        
        $paymentId = $request->get('payment_id');
        $payment = Payment::where('external_id', $paymentId)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            if ($payment->subscription) {
                $payment->subscription->update(['status' => 'active']);
            }
        }
        
        return redirect()->route('payment.success', ['payment_id' => $paymentId])
            ->with('success', 'Тестовый платёж успешно обработан!');
    }
    
    /**
     * Симуляция отмены платежа
     */
    public function cancel(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }
        
        $paymentId = $request->get('payment_id');
        $payment = Payment::where('external_id', $paymentId)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'cancelled',
                'failed_at' => now(),
                'failure_reason' => 'Отменено пользователем (тест)',
            ]);
        }
        
        return redirect()->route('payment.cancel', ['payment_id' => $paymentId]);
    }
}
