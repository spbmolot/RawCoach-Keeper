<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ $recipe->title }} — RawPlan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #10b981;
        }
        .header h1 {
            font-size: 22px;
            color: #065f46;
            margin-bottom: 8px;
        }
        .meta {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            background: #ecfdf5;
            border-right: 2px solid #fff;
        }
        .meta-item:last-child {
            border-right: none;
        }
        .meta-value {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
        }
        .meta-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .nutrition {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .nutrition-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            background: #f9fafb;
            border-right: 1px solid #e5e7eb;
        }
        .nutrition-item:last-child {
            border-right: none;
        }
        .nutrition-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .nutrition-label {
            font-size: 10px;
            color: #999;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #065f46;
            padding: 8px 12px;
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            margin-bottom: 12px;
        }
        .description {
            color: #555;
            font-size: 12px;
            margin-bottom: 20px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 6px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        table th {
            background: #f3f4f6;
            font-weight: bold;
            color: #555;
            font-size: 10px;
            text-transform: uppercase;
        }
        .step {
            margin-bottom: 12px;
            padding-left: 10px;
        }
        .step-number {
            display: inline-block;
            width: 22px;
            height: 22px;
            background: #10b981;
            color: #fff;
            text-align: center;
            line-height: 22px;
            border-radius: 50%;
            font-size: 11px;
            font-weight: bold;
            margin-right: 8px;
        }
        .step-text {
            display: inline;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 10px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .tags {
            margin-top: 5px;
        }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            background: #ecfdf5;
            color: #065f46;
            font-size: 10px;
            border-radius: 10px;
            margin-right: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $recipe->title }}</h1>
        @if($recipe->dietary_tags && count($recipe->dietary_tags) > 0)
            <div class="tags">
                @foreach($recipe->dietary_tags as $tag)
                    <span class="tag">{{ $tag }}</span>
                @endforeach
            </div>
        @endif
    </div>

    @if($recipe->description)
        <div class="description">{{ $recipe->description }}</div>
    @endif

    <div class="meta">
        <div class="meta-item">
            <div class="meta-value">{{ $recipe->prep_time }}</div>
            <div class="meta-label">Подготовка (мин)</div>
        </div>
        <div class="meta-item">
            <div class="meta-value">{{ $recipe->cook_time }}</div>
            <div class="meta-label">Готовка (мин)</div>
        </div>
        <div class="meta-item">
            <div class="meta-value">{{ $recipe->prep_time + $recipe->cook_time }}</div>
            <div class="meta-label">Всего (мин)</div>
        </div>
        <div class="meta-item">
            <div class="meta-value">{{ $recipe->servings }}</div>
            <div class="meta-label">Порций</div>
        </div>
    </div>

    <div class="nutrition">
        <div class="nutrition-item">
            <div class="nutrition-value">{{ round($recipe->calories) }}</div>
            <div class="nutrition-label">Ккал</div>
        </div>
        <div class="nutrition-item">
            <div class="nutrition-value">{{ $recipe->proteins }}г</div>
            <div class="nutrition-label">Белки</div>
        </div>
        <div class="nutrition-item">
            <div class="nutrition-value">{{ $recipe->fats }}г</div>
            <div class="nutrition-label">Жиры</div>
        </div>
        <div class="nutrition-item">
            <div class="nutrition-value">{{ $recipe->carbs }}г</div>
            <div class="nutrition-label">Углеводы</div>
        </div>
        @if($recipe->fiber)
        <div class="nutrition-item">
            <div class="nutrition-value">{{ $recipe->fiber }}г</div>
            <div class="nutrition-label">Клетчатка</div>
        </div>
        @endif
    </div>

    @if($recipe->ingredients && $recipe->ingredients->count() > 0)
    <div class="section">
        <div class="section-title">Ингредиенты</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 60%">Ингредиент</th>
                    <th style="width: 20%">Количество</th>
                    <th style="width: 20%">Единица</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recipe->ingredients as $ri)
                    <tr>
                        <td>{{ $ri->ingredient_name ?? '—' }}</td>
                        <td>{{ $ri->amount ? round($ri->amount, 1) : '—' }}</td>
                        <td>{{ $ri->unit ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($recipe->instructions)
    <div class="section">
        <div class="section-title">Приготовление</div>
        @php
            $steps = is_array($recipe->instructions)
                ? $recipe->instructions
                : preg_split('/\r?\n/', $recipe->instructions);
            $steps = array_filter($steps, fn($s) => trim($s) !== '');
        @endphp
        @foreach($steps as $i => $step)
            <div class="step">
                <span class="step-number">{{ $i + 1 }}</span>
                <span class="step-text">{{ trim($step) }}</span>
            </div>
        @endforeach
    </div>
    @endif

    @if($recipe->notes)
    <div class="section">
        <div class="section-title">Заметки</div>
        <div class="description">{{ $recipe->notes }}</div>
    </div>
    @endif

    <div class="footer">
        <p>Сгенерировано на RawPlan.ru • {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
