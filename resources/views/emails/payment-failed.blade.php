@extends('emails.layout')

@section('title', 'Проблема с оплатой')

@section('content')
    <h2>Проблема с обработкой платежа</h2>
    
    <p>Здравствуйте, {{ $user->name }}!</p>
    
    <p>К сожалению, при обработке вашего платежа возникла проблема. Мы уже работаем над её решением.</p>
    
    <div class="info-box">
        <h3>Детали платежа</h3>
        <p><strong>Номер платежа:</strong> #{{ $payment->id }}</p>
        <p><strong>Сумма:</strong> {{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</p>
        <p><strong>План:</strong> {{ $subscription->plan->name }}</p>
        <p><strong>Статус:</strong> <span class="status-badge status-error">Ошибка оплаты</span></p>
        @if($payment->failure_reason)
            <p><strong>Причина:</strong> {{ $payment->failure_reason }}</p>
        @endif
    </div>
    
    <p><strong>Что делать дальше?</strong></p>
    
    <ol>
        <li>Проверьте данные вашей банковской карты</li>
        <li>Убедитесь, что на карте достаточно средств</li>
        <li>Попробуйте совершить платеж еще раз</li>
        <li>Если проблема повторится, свяжитесь с нашей поддержкой</li>
    </ol>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('subscriptions.retry-payment', $payment) }}" class="button">
            Повторить оплату
        </a>
    </div>
    
    <p><strong>Возможные причины неудачной оплаты:</strong></p>
    <ul>
        <li>Недостаточно средств на карте</li>
        <li>Карта заблокирована или просрочена</li>
        <li>Превышен лимит по карте</li>
        <li>Технические проблемы банка</li>
        <li>Неверно введены данные карты</li>
    </ul>
    
    <div class="info-box">
        <h3>Нужна помощь?</h3>
        <p>Если у вас возникли трудности с оплатой, наша команда поддержки готова помочь:</p>
        <p>📧 Email: <a href="mailto:support@rawplan.ru">support@rawplan.ru</a></p>
        <p>📱 Telegram: <a href="https://t.me/rawplan_support">@rawplan_support</a></p>
        <p>⏰ Время работы: ежедневно с 9:00 до 21:00</p>
    </div>
    
    <p>Мы ценим ваше терпение и готовы помочь решить любые вопросы с оплатой.</p>
@endsection
