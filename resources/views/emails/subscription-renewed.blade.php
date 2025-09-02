@extends('emails.layout')

@section('title', 'Подписка успешно продлена')

@section('content')
    <h2>Подписка успешно продлена!</h2>
    
    <p>Здравствуйте, {{ $user->name }}!</p>
    
    <p>Отличные новости! Ваша подписка на план "{{ $plan->name }}" была успешно продлена.</p>
    
    <div class="info-box">
        <h3>Детали продления</h3>
        <p><strong>План:</strong> {{ $plan->name }}</p>
        <p><strong>Новый период:</strong> с {{ $subscription->starts_at->format('d.m.Y') }} по {{ $subscription->ends_at->format('d.m.Y') }}</p>
        <p><strong>Статус:</strong> <span class="status-badge status-success">Активна</span></p>
        <p><strong>Автопродление:</strong> 
            @if($subscription->auto_renewal)
                <span class="status-badge status-success">Включено</span>
            @else
                <span class="status-badge status-warning">Отключено</span>
            @endif
        </p>
    </div>
    
    <p>Теперь у вас снова есть полный доступ ко всем возможностям вашего плана:</p>
    
    <ul>
        <li>✅ Новые планы питания каждую неделю</li>
        <li>✅ Подробные рецепты с граммовкой</li>
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
    
    <div class="info-box">
        <h3>Что нового в этом месяце?</h3>
        <p>• 🥗 Новые весенние рецепты с сезонными овощами</p>
        <p>• 🍲 Специальные планы для детокса</p>
        <p>• 📱 Обновленное мобильное приложение</p>
        <p>• 👩‍⚕️ Вебинары с нутрициологами</p>
    </div>
    
    <p>Мы постоянно работаем над улучшением наших планов питания и добавлением новых функций, чтобы сделать ваш путь к здоровому образу жизни еще более комфортным и эффективным.</p>
    
    <p>Если у вас есть пожелания или предложения по улучшению наших планов, мы всегда рады вашей обратной связи!</p>
    
    <p>Желаем вам успехов и отличных результатов с RawPlan!</p>
@endsection
