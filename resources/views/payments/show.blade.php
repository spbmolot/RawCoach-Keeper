@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Платеж #{{ $payment->id }}</h1>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><dt class="font-semibold">Статус</dt><dd>{{ $payment->getStatusLabel() }}</dd></div>
        <div><dt class="font-semibold">Сумма</dt><dd>{{ number_format($payment->amount, 2, ',', ' ') }} {{ strtoupper($payment->currency) }}</dd></div>
        <div><dt class="font-semibold">Провайдер</dt><dd>{{ $payment->provider }}</dd></div>
        <div><dt class="font-semibold">Внешний ID</dt><dd>{{ $payment->external_id ?? '—' }}</dd></div>
        <div><dt class="font-semibold">Оплачен</dt><dd>{{ $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : '—' }}</dd></div>
        <div><dt class="font-semibold">Ошибка</dt><dd>{{ $payment->failure_reason ?? '—' }}</dd></div>
    </dl>
    @if($payment->payment_url && $payment->status === 'pending')
        <div class="mt-6">
            <a href="{{ $payment->payment_url }}" class="px-4 py-2 bg-amber-500 text-white rounded">Перейти к оплате</a>
        </div>
    @endif
</div>
@endsection
