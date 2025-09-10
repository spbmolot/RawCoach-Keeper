@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Оплата</h1>
    <p class="mb-4">Сумма к оплате: <strong>{{ number_format($payment->amount, 2, ',', ' ') }} {{ strtoupper($payment->currency) }}</strong></p>
    <p class="mb-6">Описание: {{ $payment->description }}</p>

    <div class="space-x-4">
        <button id="pay-yookassa" class="px-4 py-2 bg-amber-500 text-white rounded">Оплатить через YooKassa</button>
        <button id="pay-cloudpayments" class="px-4 py-2 bg-gray-700 text-white rounded">Оплатить через CloudPayments</button>
    </div>
</div>

<script>
    async function createPayment(provider) {
        const resp = await fetch("{{ route('payment.create', $payment) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ provider })
        });
        const data = await resp.json();
        if (data.success && data.payment_url) {
            window.location.href = data.payment_url;
        } else {
            alert(data.error || 'Ошибка инициализации платежа');
        }
    }
    document.getElementById('pay-yookassa').onclick = () => createPayment('yookassa');
    document.getElementById('pay-cloudpayments').onclick = () => createPayment('cloudpayments');
</script>
@endsection
