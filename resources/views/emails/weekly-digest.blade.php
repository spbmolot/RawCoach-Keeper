@extends('emails.layout')

@section('title', 'Ваше меню на неделю')

@section('content')
    <h2>{{ $user->first_name ?? $user->name }}, ваше меню на неделю 📅</h2>

    @if($menuTitle)
        <p>Меню: <strong>{{ $menuTitle }}</strong></p>
    @endif

    @if($weekDays->isNotEmpty())
        @if(!$hasSubscription)
            <div style="background-color: #fef3c7; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <p style="margin: 0; color: #92400e; font-size: 14px;">
                    🔓 Вы видите превью первого дня. <a href="{{ config('app.url') }}/subscriptions" style="color: #92400e; font-weight: 600;">Оформите подписку</a>, чтобы получить полное меню на неделю.
                </p>
            </div>
        @endif

        @foreach($weekDays as $day)
            <div style="background-color: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                <h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 16px;">
                    День {{ $day->day_number }}{{ $day->title ? ': ' . $day->title : '' }}
                </h3>

                @if($day->total_calories)
                    <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px;">
                        {{ number_format($day->total_calories, 0) }} ккал •
                        Б {{ number_format($day->total_proteins ?? 0, 0) }}г •
                        Ж {{ number_format($day->total_fats ?? 0, 0) }}г •
                        У {{ number_format($day->total_carbs ?? 0, 0) }}г
                    </p>
                @endif

                @foreach($day->dayMeals->groupBy('meal_type') as $type => $meals)
                    @php
                        $typeLabels = ['breakfast' => '🌅 Завтрак', 'lunch' => '🥗 Обед', 'dinner' => '🍽️ Ужин', 'snack' => '🥜 Перекус'];
                    @endphp
                    <p style="margin: 4px 0; font-size: 14px;">
                        <strong>{{ $typeLabels[$type] ?? $type }}:</strong>
                        {{ $meals->pluck('recipe.title')->filter()->join(', ') }}
                    </p>
                @endforeach
            </div>
        @endforeach
    @else
        <p>На этой неделе пока нет доступных дней меню.</p>
    @endif

    @if($newRecipes->isNotEmpty())
        <div class="divider"></div>

        <h3>🆕 Новые рецепты</h3>

        @foreach($newRecipes as $recipe)
            <div style="display: flex; margin-bottom: 12px; padding: 12px; background-color: #f9fafb; border-radius: 8px;">
                <div>
                    <p style="margin: 0 0 4px 0; font-weight: 600;">{{ $recipe->title }}</p>
                    @if($recipe->description)
                        <p style="margin: 0; color: #6b7280; font-size: 13px;">{{ \Illuminate\Support\Str::limit($recipe->description, 80) }}</p>
                    @endif
                    <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 12px;">{{ number_format($recipe->calories, 0) }} ккал</p>
                </div>
            </div>
        @endforeach
    @endif

    <p style="text-align: center; margin-top: 20px;">
        <a href="{{ config('app.url') }}/dashboard" class="button">Открыть личный кабинет</a>
    </p>

    @if(!$hasSubscription)
        <div class="divider"></div>
        <p style="text-align: center;">
            Хотите полное меню на каждый день?<br>
            <a href="{{ config('app.url') }}/subscriptions" style="color: #f59e0b; font-weight: 600;">Попробуйте 7 дней бесплатно →</a>
        </p>
    @endif
@endsection
