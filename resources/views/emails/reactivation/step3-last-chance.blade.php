@extends('emails.layout')

@section('title', 'Специальное предложение для возвращения')

@section('content')
    <h2>Последний шанс — скидка 30%! 🔥</h2>

    <p>{{ $user->first_name ?? $user->name }}, это наше последнее реактивационное письмо, и мы хотим сделать вам предложение, от которого сложно отказаться.</p>

    <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 12px; padding: 30px; text-align: center; margin: 20px 0;">
        <p style="font-size: 14px; color: #991b1b; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">Только для вернувшихся пользователей</p>
        <p style="font-size: 36px; font-weight: 700; color: #991b1b; margin: 0 0 10px 0;">Скидка 30%</p>
        <p style="color: #7f1d1d; margin: 0;">на любую подписку при возвращении</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; width: 50%;">
                <strong>Стандарт (месяц)</strong>
            </td>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;">
                <span style="text-decoration: line-through; color: #9ca3af;">1 990 ₽</span>
                <strong style="color: #dc2626;"> 1 393 ₽</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px;">
                <strong>Премиум (год)</strong>
            </td>
            <td style="padding: 12px; text-align: right;">
                <span style="text-decoration: line-through; color: #9ca3af;">17 910 ₽</span>
                <strong style="color: #dc2626;"> 12 537 ₽</strong>
            </td>
        </tr>
    </table>

    <p style="text-align: center;">
        <a href="{{ config('app.url') }}/subscriptions" class="button" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); font-size: 18px; padding: 16px 32px;">Вернуться со скидкой 30%</a>
    </p>

    <div class="divider"></div>

    <p>Если подписка не актуальна — ничего страшного. Бесплатные рецепты и превью меню останутся доступными. Мы всегда будем рады видеть вас снова! 🌿</p>
@endsection
