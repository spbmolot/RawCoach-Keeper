@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4 text-green-600">Платеж успешно завершен</h1>
    <p class="mb-2">Платеж № {{ $payment->id }} оплачен.</p>
    <p class="mb-6">Сумма: <strong>{{ number_format($payment->amount, 2, ',', ' ') }} {{ strtoupper($payment->currency) }}</strong></p>
    <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-amber-500 text-white rounded">В личный кабинет</a>
</div>
@endsection
