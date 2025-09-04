# Анализ структуры базы данных RawCoach-Keeper

## Структура проекта

### Основные директории:
- `/app` - основной код приложения
  - `/Models` - модели Eloquent
  - `/Filament` - административная панель
  - `/Http` - контроллеры и middleware
  - `/Services` - сервисы приложения
- `/database` - миграции, сидеры, фабрики
- `/resources` - представления и ресурсы
- `/routes` - маршруты приложения

## Анализ миграций и моделей

### 1. Таблица `users`
**Миграция**: `0001_01_01_000000_create_users_table.php` + `2024_01_15_000016_add_profile_fields_to_users_table.php`
**Модель**: `User.php`

Основные поля:
- Базовые: id, name, email, password
- Профиль: first_name, last_name, phone, birth_date, gender, avatar
- Физические данные: height, weight, target_weight, activity_level
- Предпочтения: dietary_preferences, allergies, disliked_foods
- Настройки: email_notifications, sms_notifications, push_notifications, timezone, language
- Статистика: last_login_at, last_login_ip, is_active

### 2. Таблица `plans`
**Миграция**: `2025_08_31_125413_create_plans_table.php` + `2025_09_04_124754_add_missing_fields_to_plans_table.php`
**Модель**: `Plan.php`

Поля:
- name, slug, type (monthly, yearly, individual, trial)
- price, original_price, currency
- duration_days
- features (JSON), limits (JSON)
- is_active, sort_order, description

**Проблемы обнаружены**: 
- В миграции `2025_09_04_124754` пытается добавить поле `limits`, которое уже существует в основной миграции

### 3. Таблица `coupons`
**Миграция**: `2024_01_15_000001_create_coupons_table.php` + `2025_09_04_124824_add_missing_fields_to_coupons_table.php`
**Модель**: `Coupon.php`

Поля:
- code, type (percentage, fixed), value
- usage_limit, used_count, usage_limit_per_user
- applicable_plans (JSON)
- minimum_amount, maximum_discount
- valid_from, valid_until
- is_active, created_by_type, created_by_id

**Проблемы обнаружены**:
- Миграция `2025_09_04_124824` пытается переименовать поля, но они уже созданы с правильными именами

### 4. Таблица `user_subscriptions`
**Миграция**: `2025_08_31_125415_create_user_subscriptions_table.php`
**Модель**: `UserSubscription.php`

Поля:
- user_id, plan_id
- status, started_at, ends_at
- provider, external_id

### 5. Таблица `payments`
**Миграция**: `2025_08_31_125416_create_payments_table.php` + `2025_08_31_125418_add_coupon_fields_to_payments_table.php`
**Модель**: `Payment.php`

Поля:
- user_id, plan_id
- provider, external_id
- amount, currency, status
- payload (JSON)
- paid_at
- Купонные поля: coupon_id, original_amount, discount_amount

### 6. Таблица `menus`
**Миграция**: `2024_01_15_000003_create_menus_table.php`
**Модель**: `Menu.php`

Поля:
- title, slug, description
- type (weekly, monthly, custom)
- start_date, end_date
- is_published, published_at
- created_by, approved_by, approved_at
- total_calories, total_proteins, total_fats, total_carbs
- tags (JSON), settings (JSON)

### 7. Таблица `recipes`
**Миграция**: `2024_01_15_000005_create_recipes_table.php`
**Модель**: `Recipe.php`

Поля:
- title, slug, description, instructions
- prep_time, cook_time, servings
- calories, proteins, fats, carbs
- category, difficulty, cuisine
- tags (JSON), allergens (JSON)
- image, gallery_images (JSON)
- is_published, published_at
- views_count, rating, ratings_count

### 8. Таблица `questionnaires`
**Миграция**: `2024_01_15_000008_create_questionnaires_table.php`
**Модель**: `Questionnaire.php`

Поля:
- user_id
- age, gender, height, current_weight, target_weight
- activity_level, goal
- dietary_restrictions (JSON), allergies (JSON), disliked_foods (JSON)
- health_conditions (JSON), medications (JSON)
- meal_frequency, cooking_time, budget_range
- water_intake_goal
- is_completed, completed_at

### 9. Таблица `personal_plans`
**Миграция**: `2024_01_15_000009_create_personal_plans_table.php`
**Модель**: `PersonalPlan.php`

Поля:
- user_id, questionnaire_id
- title, description
- start_date, end_date
- target_calories, target_proteins, target_fats, target_carbs
- status (draft, active, completed, paused)
- generated_by (ai, nutritionist)
- approved_by, approved_at
- notes, is_public

### 10. Связующие таблицы:
- `menu_plans` - связь меню с планами подписки
- `day_meals` - блюда в днях меню
- `recipe_ingredients` - ингредиенты рецептов
- `personal_plan_days` - дни персональных планов
- `personal_plan_day_recipes` - рецепты в днях персональных планов
- `user_favorite_menus` - избранные меню пользователей
- `user_favorite_recipes` - избранные рецепты пользователей
- `coupon_usages` - использования купонов

## Обнаруженные проблемы:

### 1. Дублирование полей в миграциях:
- `2025_09_04_124754_add_missing_fields_to_plans_table.php` - поле `limits` уже существует
- `2025_09_04_124824_add_missing_fields_to_coupons_table.php` - пытается переименовать поля, которые уже имеют правильные имена

### 2. Несоответствие моделей и миграций:
- Все основные модели соответствуют своим миграциям
- Модели `AdCampaign`, `AdCreative`, `AdPlacement` имеют минимальную реализацию

### 3. Порядок миграций:
- Миграции упорядочены корректно по датам
- Внешние ключи создаются после создания связанных таблиц

## Рекомендации:

1. Удалить или модифицировать миграции `2025_09_04_*`, так как они пытаются добавить уже существующие поля
2. Реализовать полноценные модели для рекламных сущностей (AdCampaign, AdCreative, AdPlacement)
3. Добавить индексы для часто используемых полей в запросах
4. Рассмотреть возможность добавления soft deletes для критичных сущностей
