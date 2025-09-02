@extends('emails.layout')

@section('title', 'Платеж успешно обработан')

@section('content')
    <h2>Платеж успешно обработан!</h2>
    
    <p>Здравствуйте, {{ $user->name }}!</p>
    
    <p>Мы рады сообщить, что ваш платеж был успешно обработан. Спасибо за выбор RawPlan!</p>
    
    <div class="info-box">
        <h3>Детали платежа</h3>
        <p><strong>Номер платежа:</strong> #{{ $payment->id }}</p>
        <p><strong>Сумма:</strong> {{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</p>
        <p><strong>План:</strong> {{ $subscription->plan->name }}</p>
        <p><strong>Период:</strong> с {{ $subscription->starts_at->format('d.m.Y') }} по {{ $subscription->ends_at->format('d.m.Y') }}</p>
        <p><strong>Статус:</strong> <span class="status-badge status-success">Оплачено</span></p>
    </div>
    
    <p>Ваша подписка активна и готова к использованию. Теперь у вас есть доступ ко всем возможностям выбранного плана:</p>
    
    <ul>
        <li>✅ Подробные рецепты с граммовкой</li>
        <li>✅ Планы питания на каждый день</li>
        <li>✅ Автоматические списки покупок</li>
        <li>✅ Калорийность 1200-1400 ккал/день</li>
        @if($subscription->plan->limits['archive_access'] ?? false)
            <li>✅ Доступ к архиву планов</li>
        @endif
        @if($subscription->plan->limits['early_access'] ?? false)
            <li>✅ Ранний доступ к новинкам</li>
        @endif
        @if($subscription->plan->limits['personal_plans'] ?? false)
            <li>✅ Персональные планы питания</li>
        @endif
    </ul>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('dashboard') }}" class="button">
            Перейти в личный кабинет
        </a>
    </div>
    
    <p>Если у вас возникнут вопросы или потребуется помощь, наша команда поддержки всегда готова помочь.</p>
    
    <p>Желаем вам успехов на пути к здоровому образу жизни!</p>
@endsection
