# Production Checklist - RawPlan

## Перед запуском

### Конфигурация (.env)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://rawplan.ru`
- [ ] Настроить реальные ключи YooKassa:
  - `YOOKASSA_SHOP_ID`
  - `YOOKASSA_SECRET_KEY`
  - `YOOKASSA_WEBHOOK_SECRET`
- [ ] Настроить аналитику:
  - `GOOGLE_ANALYTICS_ID`
  - `YANDEX_METRIKA_ID`
- [ ] Настроить почту (SMTP):
  - `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`

### Кэширование
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### База данных
```bash
php artisan migrate --force
php artisan db:seed --class=PlanSeeder --force
```

### Фронтенд
```bash
npm run build
```

### Безопасность
- [ ] HTTPS настроен и работает
- [ ] CSRF защита включена
- [ ] Пароли хранятся в bcrypt
- [ ] API токены через Sanctum
- [ ] Rate limiting настроен

### Мониторинг
- [ ] Логи настроены (`storage/logs/`)
- [ ] Yandex.Metrika подключена
- [ ] Google Analytics подключена
- [ ] Настроены уведомления об ошибках

### Платежи
- [ ] Webhook YooKassa настроен: `https://rawplan.ru/webhook/yookassa`
- [ ] Тестовый платёж прошёл успешно
- [ ] Возврат средств работает

### SEO
- [ ] sitemap.xml доступен: `https://rawplan.ru/sitemap.xml`
- [ ] robots.txt настроен
- [ ] Meta-теги на всех страницах
- [ ] Open Graph теги

## Команды для деплоя

```bash
# Очистка и кэширование
php artisan optimize:clear
php artisan optimize

# Миграции
php artisan migrate --force

# Сборка фронтенда
npm run build

# Перезапуск очередей (если используются)
php artisan queue:restart
```

## Контакты поддержки

- Email: support@rawplan.ru
- Telegram: @rawplan_support

## Версия

- Laravel: 11.x
- PHP: 8.3
- Node.js: 18+
