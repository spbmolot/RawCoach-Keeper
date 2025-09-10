@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">История платежей</h1>
    <table class="w-full text-left border">
        <thead>
            <tr class="border-b">
                <th class="p-2">ID</th>
                <th class="p-2">Дата</th>
                <th class="p-2">Сумма</th>
                <th class="p-2">Статус</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $p)
            <tr class="border-b">
                <td class="p-2">{{ $p->id }}</td>
                <td class="p-2">{{ $p->created_at->format('d.m.Y H:i') }}</td>
                <td class="p-2">{{ number_format($p->amount, 2, ',', ' ') }} {{ strtoupper($p->currency) }}</td>
                <td class="p-2">{{ $p->getStatusLabel() }}</td>
                <td class="p-2"><a class="text-amber-600" href="{{ route('payment.show', $p) }}">Подробнее</a></td>
            </tr>
            @empty
            <tr><td class="p-2" colspan="5">Платежей нет</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
