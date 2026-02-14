@extends('emails.layout')

@section('title', 'Что входит в подписку RawPlan')

@section('content')
    <h2>Что вы получаете с подпиской RawPlan</h2>

    <p>{{ $user->first_name ?? $user->name }}, надеемся, вам понравились наши бесплатные рецепты! Вот что открывается с полной подпиской:</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                <strong>✅ Полное месячное меню</strong><br>
                <span style="color: #6b7280;">30 дней сбалансированного питания</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                <strong>✅ 200+ рецептов</strong><br>
                <span style="color: #6b7280;">С пошаговыми инструкциями и КБЖУ</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                <strong>✅ Списки покупок</strong><br>
                <span style="color: #6b7280;">Автоматическая генерация на день и неделю</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                <strong>✅ Экспорт в PDF</strong><br>
                <span style="color: #6b7280;">Скачивайте меню и берите с собой</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px;">
                <strong>✅ Новые меню каждый месяц</strong><br>
                <span style="color: #6b7280;">Без повторений — всегда свежие идеи</span>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <h3>💰 Стоимость</h3>
        <p>
            <strong>Стандарт:</strong> 1 990 ₽/мес<br>
            <strong>Премиум (год):</strong> 17 910 ₽ — экономия 25%<br>
            <strong>Пробный период:</strong> 7 дней бесплатно
        </p>
    </div>

    <p style="text-align: center;">
        <a href="{{ config('app.url') }}/subscriptions" class="button">Попробовать бесплатно 7 дней</a>
    </p>

    <p style="color: #6b7280; font-size: 14px; text-align: center;">Отмена в любой момент. Без скрытых платежей.</p>
@endsection
