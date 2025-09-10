@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4 text-gray-700">Платеж отменен</h1>
    <p class="mb-6">Вы отменили платеж. При необходимости попробуйте оплатить снова из истории платежей.</p>
    <a href="{{ route('payment.history') }}" class="px-4 py-2 bg-gray-700 text-white rounded">История платежей</a>
</div>
@endsection
