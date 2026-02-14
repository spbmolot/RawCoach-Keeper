@extends('emails.layout')

@section('title', 'Мы заметили, что вас не было')

@section('content')
    <h2>{{ $user->first_name ?? $user->name }}, ваш план ждёт! 🍽️</h2>

    <p>Мы заметили, что вы не заходили в RawPlan уже <strong>{{ $daysInactive }} {{ trans_choice('день|дня|дней', $daysInactive) }}</strong>.</p>

    <div class="info-box">
        <h3>Не пропустите сегодняшнее меню!</h3>
        <p>Каждый пропущенный день — это упущенная возможность следовать плану. Но никогда не поздно вернуться!</p>
    </div>

    <p>Вот несколько причин зайти прямо сейчас:</p>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 10px; vertical-align: top; width: 30px;">📋</td>
            <td style="padding: 10px;">
                <strong>Меню на сегодня</strong><br>
                <span style="color: #6b7280;">Готовый план питания ждёт вас</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px; vertical-align: top;">🛒</td>
            <td style="padding: 10px;">
                <strong>Список покупок</strong><br>
                <span style="color: #6b7280;">Сгенерируйте его за секунду</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px; vertical-align: top;">⭐</td>
            <td style="padding: 10px;">
                <strong>Избранные рецепты</strong><br>
                <span style="color: #6b7280;">Ваша коллекция всё ещё здесь</span>
            </td>
        </tr>
    </table>

    <p style="text-align: center;">
        <a href="{{ config('app.url') }}/dashboard/today" class="button">Посмотреть сегодняшнее меню</a>
    </p>

    <div class="divider"></div>

    <p style="color: #6b7280; font-size: 14px;">💡 <strong>Совет:</strong> даже если вы пропустили несколько дней, вы всегда можете продолжить с текущего дня. Не нужно начинать сначала!</p>
@endsection
