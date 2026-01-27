<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Day;
use App\Models\DayMeal;
use App\Models\Recipe;
use Carbon\Carbon;

class FebruaryMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём меню на февраль 2026
        $menu = Menu::create([
            'title' => 'Меню на февраль 2026',
            'slug' => 'menu-february-2026',
            'description' => 'Сбалансированное зимнее меню на февраль 2026 года. Включает согревающие супы, питательные каши, лёгкие салаты и полезные перекусы. Калорийность 1200-1400 ккал в день для комфортного похудения.',
            'month' => 2,
            'year' => 2026,
            'total_calories' => 1300,
            'total_proteins' => 80,
            'total_fats' => 45,
            'total_carbs' => 150,
            'is_published' => true,
            'is_personal' => false,
            'published_at' => now(),
            'visible_from' => Carbon::create(2026, 2, 1), // Видимо с 1 февраля
            'features' => [
                'Зимние сезонные продукты',
                'Согревающие блюда',
                'Богато витаминами',
                'Поддержка иммунитета',
            ],
            'notes' => 'Меню разработано с учётом зимнего сезона. Используются доступные продукты.',
        ]);

        // Получаем существующие рецепты
        $recipes = Recipe::where('is_published', true)->get();
        
        if ($recipes->isEmpty()) {
            $this->command->warn('Нет опубликованных рецептов! Создаю базовые рецепты...');
            $this->createBaseRecipes();
            $recipes = Recipe::where('is_published', true)->get();
        }

        // Группируем рецепты по категориям
        $breakfasts = $recipes->where('category', 'breakfast');
        $lunches = $recipes->where('category', 'lunch');
        $dinners = $recipes->where('category', 'dinner');
        $snacks = $recipes->where('category', 'snack');

        // Если категорий нет, используем все рецепты
        if ($breakfasts->isEmpty()) $breakfasts = $recipes;
        if ($lunches->isEmpty()) $lunches = $recipes;
        if ($dinners->isEmpty()) $dinners = $recipes;
        if ($snacks->isEmpty()) $snacks = $recipes;

        // Создаём 28 дней (февраль 2026)
        for ($dayNum = 1; $dayNum <= 28; $dayNum++) {
            $day = Day::create([
                'menu_id' => $menu->id,
                'day_number' => $dayNum,
                'title' => $this->getDayTitle($dayNum),
                'description' => $this->getDayDescription($dayNum),
                'total_calories' => rand(1200, 1400),
                'total_proteins' => rand(70, 90),
                'total_fats' => rand(40, 50),
                'total_carbs' => rand(140, 160),
                'is_active' => true,
                'notes' => null,
            ]);

            // Завтрак
            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $breakfasts->values()->get($dayNum % $breakfasts->count())->id,
                'meal_type' => 'breakfast',
                'order' => 1,
                'portion_size' => 1.0,
                'notes' => null,
            ]);

            // Обед
            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $lunches->values()->get($dayNum % $lunches->count())->id,
                'meal_type' => 'lunch',
                'order' => 2,
                'portion_size' => 1.0,
                'notes' => null,
            ]);

            // Перекус (один на день)
            if ($snacks->isNotEmpty()) {
                DayMeal::create([
                    'day_id' => $day->id,
                    'recipe_id' => $snacks->values()->get($dayNum % $snacks->count())->id,
                    'meal_type' => 'snack',
                    'order' => 3,
                    'portion_size' => 0.5,
                    'notes' => null,
                ]);
            }

            // Ужин
            DayMeal::create([
                'day_id' => $day->id,
                'recipe_id' => $dinners->values()->get($dayNum % $dinners->count())->id,
                'meal_type' => 'dinner',
                'order' => 4,
                'portion_size' => 1.0,
                'notes' => null,
            ]);
        }

        $this->command->info("Создано меню на февраль 2026 с {$menu->days()->count()} днями");
    }

    private function getDayTitle(int $dayNum): string
    {
        $weekDay = (($dayNum - 1) % 7);
        $weekNames = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $weekNum = ceil($dayNum / 7);
        
        return "День {$dayNum} - {$weekNames[$weekDay]} (неделя {$weekNum})";
    }

    private function getDayDescription(int $dayNum): string
    {
        $descriptions = [
            'Лёгкий старт недели с питательным завтраком',
            'Энергичный день с белковым обедом',
            'Середина недели - время для овощей',
            'Разгрузочный день с лёгкими блюдами',
            'Завершаем рабочую неделю вкусно',
            'Выходной день - можно побаловать себя',
            'Воскресный отдых с семейным обедом',
        ];
        
        return $descriptions[($dayNum - 1) % 7];
    }

    private function createBaseRecipes(): void
    {
        // Завтраки
        $breakfasts = [
            [
                'title' => 'Овсяная каша с ягодами',
                'slug' => 'ovsyanaya-kasha-s-yagodami',
                'description' => 'Питательная овсяная каша с замороженными ягодами и мёдом',
                'instructions' => "1. Залейте овсяные хлопья водой или молоком\n2. Варите 5-7 минут на среднем огне\n3. Добавьте ягоды и мёд\n4. Перемешайте и подавайте",
                'prep_time' => 5,
                'cook_time' => 10,
                'servings' => 1,
                'calories' => 320,
                'proteins' => 10,
                'fats' => 8,
                'carbs' => 52,
                'fiber' => 6,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'russian',
                'dietary_tags' => ['vegetarian'],
                'allergens' => ['gluten', 'dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Творожная запеканка',
                'slug' => 'tvorozhnaya-zapekanka',
                'description' => 'Нежная творожная запеканка с изюмом',
                'instructions' => "1. Смешайте творог с яйцами и сахаром\n2. Добавьте манку и изюм\n3. Выложите в форму\n4. Запекайте 30-40 минут при 180°C",
                'prep_time' => 15,
                'cook_time' => 40,
                'servings' => 4,
                'calories' => 280,
                'proteins' => 18,
                'fats' => 10,
                'carbs' => 28,
                'fiber' => 1,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'russian',
                'dietary_tags' => ['vegetarian'],
                'allergens' => ['dairy', 'eggs', 'gluten'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Яичница с овощами',
                'slug' => 'yaichnitsa-s-ovoshchami',
                'description' => 'Яичница с помидорами и зеленью',
                'instructions' => "1. Разогрейте сковороду с маслом\n2. Обжарьте нарезанные помидоры\n3. Вбейте яйца\n4. Посолите, поперчите, добавьте зелень",
                'prep_time' => 5,
                'cook_time' => 7,
                'servings' => 1,
                'calories' => 250,
                'proteins' => 14,
                'fats' => 18,
                'carbs' => 6,
                'fiber' => 2,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'russian',
                'dietary_tags' => ['low-carb', 'keto'],
                'allergens' => ['eggs'],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Обеды
        $lunches = [
            [
                'title' => 'Куриный суп с лапшой',
                'slug' => 'kurinyj-sup-s-lapshoj',
                'description' => 'Согревающий куриный суп с домашней лапшой',
                'instructions' => "1. Сварите куриный бульон\n2. Добавьте нарезанные овощи\n3. Положите лапшу\n4. Варите до готовности\n5. Подавайте с зеленью",
                'prep_time' => 20,
                'cook_time' => 45,
                'servings' => 4,
                'calories' => 180,
                'proteins' => 15,
                'fats' => 5,
                'carbs' => 18,
                'fiber' => 2,
                'difficulty' => 'medium',
                'category' => 'lunch',
                'cuisine' => 'russian',
                'dietary_tags' => [],
                'allergens' => ['gluten'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Гречка с курицей',
                'slug' => 'grechka-s-kuritsej',
                'description' => 'Гречневая каша с тушёной курицей и овощами',
                'instructions' => "1. Отварите гречку\n2. Обжарьте курицу с луком\n3. Добавьте морковь и специи\n4. Тушите 15 минут\n5. Подавайте с гречкой",
                'prep_time' => 10,
                'cook_time' => 30,
                'servings' => 2,
                'calories' => 380,
                'proteins' => 32,
                'fats' => 10,
                'carbs' => 40,
                'fiber' => 5,
                'difficulty' => 'easy',
                'category' => 'lunch',
                'cuisine' => 'russian',
                'dietary_tags' => ['gluten-free', 'high-protein'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Борщ украинский',
                'slug' => 'borshch-ukrainskij',
                'description' => 'Классический борщ со свёклой и сметаной',
                'instructions' => "1. Сварите мясной бульон\n2. Добавьте картофель и капусту\n3. Обжарьте свёклу с морковью и луком\n4. Соедините всё вместе\n5. Добавьте томатную пасту и специи\n6. Подавайте со сметаной",
                'prep_time' => 30,
                'cook_time' => 60,
                'servings' => 6,
                'calories' => 220,
                'proteins' => 12,
                'fats' => 8,
                'carbs' => 25,
                'fiber' => 4,
                'difficulty' => 'medium',
                'category' => 'lunch',
                'cuisine' => 'russian',
                'dietary_tags' => [],
                'allergens' => ['dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Ужины
        $dinners = [
            [
                'title' => 'Запечённая рыба с овощами',
                'slug' => 'zapechyonnaya-ryba-s-ovoshchami',
                'description' => 'Филе рыбы, запечённое с брокколи и цветной капустой',
                'instructions' => "1. Выложите рыбу на противень\n2. Добавьте овощи\n3. Полейте оливковым маслом\n4. Посолите и поперчите\n5. Запекайте 25 минут при 200°C",
                'prep_time' => 15,
                'cook_time' => 25,
                'servings' => 2,
                'calories' => 280,
                'proteins' => 35,
                'fats' => 12,
                'carbs' => 8,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'dinner',
                'cuisine' => 'european',
                'dietary_tags' => ['low-carb', 'high-protein', 'gluten-free'],
                'allergens' => ['fish'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Куриные котлеты на пару',
                'slug' => 'kurinye-kotlety-na-paru',
                'description' => 'Диетические куриные котлеты с зеленью',
                'instructions' => "1. Измельчите куриное филе\n2. Добавьте лук, яйцо и зелень\n3. Сформируйте котлеты\n4. Готовьте на пару 20-25 минут",
                'prep_time' => 20,
                'cook_time' => 25,
                'servings' => 4,
                'calories' => 180,
                'proteins' => 28,
                'fats' => 6,
                'carbs' => 4,
                'fiber' => 1,
                'difficulty' => 'easy',
                'category' => 'dinner',
                'cuisine' => 'russian',
                'dietary_tags' => ['low-carb', 'high-protein'],
                'allergens' => ['eggs'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Овощное рагу',
                'slug' => 'ovoshchnoe-ragu',
                'description' => 'Тушёные сезонные овощи с травами',
                'instructions' => "1. Нарежьте овощи кубиками\n2. Обжарьте лук и морковь\n3. Добавьте остальные овощи\n4. Тушите 30 минут\n5. Добавьте специи и зелень",
                'prep_time' => 20,
                'cook_time' => 35,
                'servings' => 3,
                'calories' => 150,
                'proteins' => 5,
                'fats' => 6,
                'carbs' => 20,
                'fiber' => 6,
                'difficulty' => 'easy',
                'category' => 'dinner',
                'cuisine' => 'russian',
                'dietary_tags' => ['vegetarian', 'vegan', 'gluten-free'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Перекусы
        $snacks = [
            [
                'title' => 'Греческий йогурт с орехами',
                'slug' => 'grecheskij-jogurt-s-orehami',
                'description' => 'Натуральный йогурт с грецкими орехами и мёдом',
                'instructions' => "1. Выложите йогурт в миску\n2. Добавьте измельчённые орехи\n3. Полейте мёдом",
                'prep_time' => 3,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 180,
                'proteins' => 12,
                'fats' => 10,
                'carbs' => 12,
                'fiber' => 1,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'european',
                'dietary_tags' => ['vegetarian'],
                'allergens' => ['dairy', 'nuts'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Яблоко с арахисовой пастой',
                'slug' => 'yabloko-s-arahisovoj-pastoj',
                'description' => 'Свежее яблоко с натуральной арахисовой пастой',
                'instructions' => "1. Нарежьте яблоко дольками\n2. Подавайте с арахисовой пастой",
                'prep_time' => 2,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 200,
                'proteins' => 5,
                'fats' => 12,
                'carbs' => 22,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'american',
                'dietary_tags' => ['vegetarian', 'vegan'],
                'allergens' => ['peanuts'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Морковные палочки с хумусом',
                'slug' => 'morkovnye-palochki-s-humusom',
                'description' => 'Свежая морковь с домашним хумусом',
                'instructions' => "1. Нарежьте морковь палочками\n2. Подавайте с хумусом",
                'prep_time' => 5,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 120,
                'proteins' => 4,
                'fats' => 6,
                'carbs' => 14,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'middle-eastern',
                'dietary_tags' => ['vegetarian', 'vegan', 'gluten-free'],
                'allergens' => ['sesame'],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        foreach (array_merge($breakfasts, $lunches, $dinners, $snacks) as $recipe) {
            Recipe::create($recipe);
        }

        $this->command->info('Создано ' . count($breakfasts) + count($lunches) + count($dinners) + count($snacks) . ' базовых рецептов');
    }
}
