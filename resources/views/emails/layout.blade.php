<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RawPlan')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            color: #1f2937;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .content p {
            margin-bottom: 16px;
            font-size: 16px;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-1px);
        }
        .info-box {
            background-color: #f3f4f6;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 18px;
        }
        .info-box p {
            margin: 0;
            color: #6b7280;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #f59e0b;
            text-decoration: none;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6b7280;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-error {
            background-color: #fee2e2;
            color: #991b1b;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .content h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RawPlan</h1>
            <p>Планы питания для здорового образа жизни</p>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>С уважением,<br>Команда RawPlan</p>
            
            <div class="social-links">
                <a href="#">Telegram</a>
                <a href="#">Instagram</a>
                <a href="#">ВКонтакте</a>
            </div>
            
            <div class="divider"></div>
            
            <p>
                Если у вас есть вопросы, свяжитесь с нами: 
                <a href="mailto:support@rawplan.ru">support@rawplan.ru</a>
            </p>
            
            <p>
                <a href="{{ config('app.url') }}">Перейти на сайт</a> | 
                <a href="{{ config('app.url') }}/unsubscribe">Отписаться от рассылки</a>
            </p>
        </div>
    </div>
</body>
</html>
