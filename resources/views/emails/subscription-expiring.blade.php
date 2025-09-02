@extends('emails.layout')

@section('title', 'Ваша подписка скоро истечет')

@section('content')
    <h2>Ваша подписка скоро истечет</h2>
    
    <p>Здравствуйте, {{ $user->name }}!</p>
    
    <p>Напоминаем, что срок действия вашей подписки на план "{{ $plan->name }}" подходит к концу.</p>
    
    <div class="info-box">
        <h3>Информация о подписке</h3>
        <p><strong>План:</strong> {{ $plan->name }}</p>
        <p><strong>Дата окончания:</strong> {{ $subscription->ends_at->format('d.m.Y') }}</p>
        <p><strong>Осталось дней:</strong> {{ $daysLeft }}</p>
        <p><strong>Автопродление:</strong> 
            @if($subscription->auto_renewal)
                <span class="status-badge status-success">Включено</span>
            @else
                <span class="status-badge status-warning">Отключено</span>
            @endif
        </p>
    </div>
    
    @if($subscription->auto_renewal)
        <p>У вас включено автоматическое продление подписки. Оплата будет произведена автоматически {{ $subscription->ends_at->subDays(1)->format('d.m.Y') }}.</p>
        
        <p>Убедитесь, что на вашей карте достаточно средств для списания {{ number_format($plan->price, 0, ',', ' ') }} ₽.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('subscriptions.manage', $subscription) }}" class="button">
                Управление подпиской
            </a>
        </div>
        
        <p>Если вы хотите отключить автопродление или изменить способ оплаты, сделайте это в личном кабинете.</p>
    @else
        <p>У вас отключено автоматическое продление. Чтобы продолжить пользоваться планами питания, необходимо продлить подписку вручную.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('subscriptions.renew', $subscription) }}" class="button">
                Продлить подписку
            </a>
        </div>
        
        <p><strong>Что произойдет после окончания подписки:</strong></p>
        <ul>
            <li>❌ Доступ к новым планам питания будет ограничен</li>
            <li>❌ Архивные планы станут недоступны</li>
            <li>❌ Персональные рекомендации будут отключены</li>
            <li>✅ Ваши сохраненные рецепты останутся доступны</li>
        </ul>
    @endif
    
    <div class="info-box">
        <h3>Почему стоит продлить подписку?</h3>
        <p>• Новые планы питания каждую неделю</p>
        <p>• Сезонные рецепты и специальные меню</p>
        <p>• Поддержка нутрициологов</p>
        <p>• Персональные рекомендации</p>
        <p>• Скидки на дополнительные услуги</p>
    </div>
    
    <p>Если у вас есть вопросы о продлении подписки или вы хотите изменить план, наша команда поддержки всегда готова помочь.</p>
    
    <p>Спасибо, что выбираете RawPlan для своего здорового питания!</p>
@endsection
