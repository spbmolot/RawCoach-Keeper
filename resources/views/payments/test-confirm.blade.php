<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тестовая оплата | RawPlan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Тестовый режим оплаты</h1>
                <p class="text-gray-500">Это страница для тестирования платежей в sandbox</p>
            </div>
            
            @if($payment)
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Сумма:</span>
                        <span class="font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Описание:</span>
                        <span class="text-gray-900">{{ $payment->description }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID платежа:</span>
                        <span class="text-gray-500 text-sm">{{ $paymentId }}</span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('payment.test.success', ['payment_id' => $paymentId]) }}" 
                       class="block w-full py-3 px-4 bg-green-500 text-white text-center rounded-xl font-semibold hover:bg-green-600 transition">
                        ✓ Симулировать успешную оплату
                    </a>
                    <a href="{{ route('payment.test.cancel', ['payment_id' => $paymentId]) }}" 
                       class="block w-full py-3 px-4 bg-red-500 text-white text-center rounded-xl font-semibold hover:bg-red-600 transition">
                        ✕ Симулировать отмену
                    </a>
                </div>
            @else
                <div class="text-center text-gray-500">
                    <p>Платёж не найден</p>
                    <a href="{{ route('dashboard') }}" class="text-green-600 hover:underline mt-4 inline-block">
                        Вернуться в кабинет
                    </a>
                </div>
            @endif
            
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">
                    Эта страница доступна только в режиме разработки.<br>
                    В production будет использоваться реальная страница YooKassa.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
