<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ $menu->title ?? $menu->name ?? 'Меню' }} — RawPlan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #10b981;
        }
        .header h1 { font-size: 20px; color: #065f46; margin-bottom: 4px; }
        .header p { color: #666; font-size: 12px; }
        .day {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .day-title {
            background: #10b981;
            color: #fff;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .day-nutrition {
            font-size: 10px;
            color: #666;
            margin-bottom: 6px;
            padding-left: 12px;
        }
        .meal {
            margin-bottom: 8px;
            padding-left: 12px;
        }
        .meal-type {
            font-weight: bold;
            color: #065f46;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .meal-recipe { font-size: 11px; }
        .meal-meta { font-size: 9px; color: #888; }
        .ingredients-list {
            margin-top: 3px;
            padding-left: 15px;
            font-size: 10px;
            color: #555;
        }
        .ingredients-list li { margin-bottom: 1px; }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #065f46;
            padding: 8px 12px;
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            margin: 20px 0 12px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table th, table td {
            padding: 4px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        table th {
            background: #f3f4f6;
            font-weight: bold;
            color: #555;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #999;
            font-size: 9px;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $menu->title ?? $menu->name ?? 'Меню' }}</h1>
        @if($menu->description)
            <p>{{ $menu->description }}</p>
        @endif
    </div>

    @php
        $mealTypeNames = [
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус',
        ];
        $mealTypeOrder = ['breakfast', 'lunch', 'snack', 'dinner'];
    @endphp

    @foreach($days as $day)
        <div class="day">
            <div class="day-title">День {{ $day->day_number }} — {{ $day->getDayOfWeekName() }}</div>
            <div class="day-nutrition">
                Ккал: {{ round($day->total_calories ?? 0) }} |
                Б: {{ round($day->total_proteins ?? 0, 1) }}г |
                Ж: {{ round($day->total_fats ?? 0, 1) }}г |
                У: {{ round($day->total_carbs ?? 0, 1) }}г
            </div>

            @php
                $groupedMeals = $day->meals->groupBy('meal_type');
            @endphp

            @foreach($mealTypeOrder as $mealType)
                @if(isset($groupedMeals[$mealType]))
                    @foreach($groupedMeals[$mealType] as $meal)
                        @if($meal->recipe)
                            <div class="meal">
                                <div class="meal-type">{{ $mealTypeNames[$mealType] ?? $mealType }}</div>
                                <div class="meal-recipe">{{ $meal->recipe->title }}</div>
                                <div class="meal-meta">
                                    {{ round($meal->recipe->calories) }} ккал |
                                    Б: {{ $meal->recipe->proteins }}г |
                                    Ж: {{ $meal->recipe->fats }}г |
                                    У: {{ $meal->recipe->carbs }}г |
                                    {{ $meal->recipe->prep_time + $meal->recipe->cook_time }} мин
                                </div>
                                @if($meal->recipe->ingredients && $meal->recipe->ingredients->count() > 0)
                                    <ul class="ingredients-list">
                                        @foreach($meal->recipe->ingredients as $ing)
                                            <li>{{ $ing->ingredient_name }} — {{ $ing->amount ? round($ing->amount, 1) . ' ' . ($ing->unit ?? '') : '' }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        <p>Сгенерировано на RawPlan.ru • {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
