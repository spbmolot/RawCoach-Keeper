<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Список покупок</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #10b981;
        }
        .header h1 {
            font-size: 24px;
            color: #065f46;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .category {
            margin-bottom: 25px;
        }
        .category-title {
            background: #ecfdf5;
            color: #065f46;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-left: 4px solid #10b981;
        }
        .items {
            padding-left: 15px;
        }
        .item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            overflow: hidden;
        }
        .item:last-child {
            border-bottom: none;
        }
        .checkbox {
            width: 14px;
            height: 14px;
            border: 2px solid #ccc;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
        }
        .item-name {
            display: inline-block;
            vertical-align: middle;
        }
        .item-amount {
            float: right;
            color: #666;
            font-weight: 500;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 10px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Список покупок</h1>
        <p>{{ $startDate->format('d.m.Y') }} — {{ $endDate->format('d.m.Y') }}</p>
    </div>

    @foreach($shoppingList as $category => $items)
        <div class="category">
            <div class="category-title">{{ $category }}</div>
            <div class="items">
                @foreach($items as $item)
                    <div class="item">
                        <span class="checkbox"></span>
                        <span class="item-name">{{ $item['ingredient']->ingredient_name ?? '—' }}</span>
                        <span class="item-amount">{{ round($item['amount'], 1) }} {{ $item['unit'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="footer">
        <p>Сгенерировано на RawPlan.ru • {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
