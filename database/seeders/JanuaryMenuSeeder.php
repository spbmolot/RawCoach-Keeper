<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Day;
use App\Models\DayMeal;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Carbon\Carbon;

class JanuaryMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Находим или создаём меню на январь 2026
        $menu = Menu::where('month', 1)->where('year', 2026)->first();
        
        if ($menu) {
            // Удаляем старые дни
            $menu->days()->delete();
            
            // Обновляем меню
            $menu->update([
                'title' => 'Меню на январь 2026 — Новогодний детокс',
                'slug' => 'menu-january-2026',
                'description' => 'Сбалансированное зимнее меню на январь 2026 года. После праздников — время лёгких и полезных блюд! Включает детокс-супы, овощные салаты, белковые блюда и полезные перекусы. Калорийность 1200-1400 ккал в день для комфортного восстановления после праздников.',
                'total_calories' => 1300,
                'total_proteins' => 85,
                'total_fats' => 45,
                'total_carbs' => 145,
                'is_published' => true,
                'is_personal' => false,
                'published_at' => now(),
                'visible_from' => null, // Видимо сразу
                'features' => [
                    'Детокс после праздников',
                    'Зимние сезонные продукты',
                    'Лёгкие и питательные блюда',
                    'Поддержка иммунитета',
                    'Богато клетчаткой',
                ],
                'notes' => 'Меню разработано для мягкого восстановления после новогодних праздников.',
            ]);
        } else {
            $menu = Menu::create([
                'title' => 'Меню на январь 2026 — Новогодний детокс',
                'slug' => 'menu-january-2026',
                'description' => 'Сбалансированное зимнее меню на январь 2026 года. После праздников — время лёгких и полезных блюд!',
                'month' => 1,
                'year' => 2026,
                'total_calories' => 1300,
                'total_proteins' => 85,
                'total_fats' => 45,
                'total_carbs' => 145,
                'is_published' => true,
                'is_personal' => false,
                'published_at' => now(),
                'visible_from' => null,
                'features' => [
                    'Детокс после праздников',
                    'Зимние сезонные продукты',
                    'Лёгкие и питательные блюда',
                ],
                'notes' => 'Меню разработано для мягкого восстановления после новогодних праздников.',
            ]);
        }

        // Создаём детальные рецепты для января
        $this->createJanuaryRecipes();
        
        // Получаем рецепты по категориям
        $breakfasts = Recipe::where('is_published', true)->where('category', 'breakfast')->get();
        $lunches = Recipe::where('is_published', true)->where('category', 'lunch')->get();
        $dinners = Recipe::where('is_published', true)->where('category', 'dinner')->get();
        $snacks = Recipe::where('is_published', true)->where('category', 'snack')->get();

        // Создаём 31 день (январь)
        for ($dayNum = 1; $dayNum <= 31; $dayNum++) {
            $day = Day::create([
                'menu_id' => $menu->id,
                'day_number' => $dayNum,
                'title' => $this->getDayTitle($dayNum),
                'description' => $this->getDayDescription($dayNum),
                'total_calories' => $this->getDayCalories($dayNum),
                'total_proteins' => rand(75, 95),
                'total_fats' => rand(40, 50),
                'total_carbs' => rand(130, 160),
                'is_active' => true,
                'notes' => $this->getDayNotes($dayNum),
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

            // Перекус
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

        $this->command->info("Меню на январь 2026 обновлено: {$menu->days()->count()} дней");
    }

    private function getDayTitle(int $dayNum): string
    {
        $weekDay = (($dayNum - 1) % 7);
        $weekNames = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $weekNum = ceil($dayNum / 7);
        
        // Особые дни
        if ($dayNum <= 8) {
            return "День {$dayNum} — Новогодние каникулы ({$weekNames[$weekDay]})";
        }
        
        return "День {$dayNum} — {$weekNames[$weekDay]} (неделя {$weekNum})";
    }

    private function getDayDescription(int $dayNum): string
    {
        if ($dayNum == 1) return 'Первый день нового года — лёгкий детокс после праздничного застолья';
        if ($dayNum == 2) return 'Продолжаем восстановление — много овощей и жидкости';
        if ($dayNum <= 8) return 'Каникулы — время для полезных и вкусных блюд';
        
        $descriptions = [
            'Энергичный старт недели с белковым завтраком',
            'Активный день — сбалансированное питание',
            'Середина недели — время для овощей и клетчатки',
            'Разгрузочный день с лёгкими блюдами',
            'Завершаем рабочую неделю питательным меню',
            'Выходной — можно приготовить что-то особенное',
            'Воскресный отдых с семейным обедом',
        ];
        
        return $descriptions[($dayNum - 1) % 7];
    }

    private function getDayCalories(int $dayNum): int
    {
        // Первые дни после праздников — меньше калорий (детокс)
        if ($dayNum <= 3) return rand(1100, 1200);
        if ($dayNum <= 8) return rand(1200, 1300);
        
        return rand(1250, 1400);
    }

    private function getDayNotes(int $dayNum): ?string
    {
        if ($dayNum == 1) return 'Пейте больше воды и травяного чая';
        if ($dayNum == 2) return 'Добавьте лимон в воду для детокса';
        if ($dayNum == 7) return 'Рождество — можно немного побаловать себя';
        
        return null;
    }

    private function createJanuaryRecipes(): void
    {
        // Дополнительные зимние завтраки
        $breakfasts = [
            [
                'title' => 'Гречневая каша с грибами',
                'slug' => 'grechnevaya-kasha-s-gribami',
                'description' => 'Питательная гречка с обжаренными шампиньонами и луком',
                'instructions' => "1. Отварите гречку до готовности\n2. Обжарьте нарезанные шампиньоны с луком\n3. Добавьте соль, перец, зелень\n4. Смешайте с гречкой и подавайте",
                'prep_time' => 10,
                'cook_time' => 20,
                'servings' => 2,
                'calories' => 290,
                'proteins' => 12,
                'fats' => 8,
                'carbs' => 42,
                'fiber' => 5,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'russian',
                'dietary_tags' => ['vegetarian', 'vegan', 'gluten-free'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Смузи-боул с бананом и шпинатом',
                'slug' => 'smuzi-boul-s-bananom-i-shpinatom',
                'description' => 'Зелёный смузи-боул с топпингами из орехов и семян',
                'instructions' => "1. Взбейте банан, шпинат и молоко в блендере\n2. Вылейте в миску\n3. Украсьте гранолой, орехами и ягодами\n4. Добавьте семена чиа",
                'prep_time' => 10,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 350,
                'proteins' => 10,
                'fats' => 12,
                'carbs' => 50,
                'fiber' => 8,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'american',
                'dietary_tags' => ['vegetarian'],
                'allergens' => ['nuts', 'dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Сырники с ягодным соусом',
                'slug' => 'syrniki-s-yagodnym-sousom',
                'description' => 'Пышные творожные сырники с домашним ягодным соусом',
                'instructions' => "1. Смешайте творог, яйцо, муку и сахар\n2. Сформируйте сырники\n3. Обжарьте на среднем огне до золотистой корочки\n4. Подавайте с ягодным соусом и сметаной",
                'prep_time' => 15,
                'cook_time' => 15,
                'servings' => 2,
                'calories' => 320,
                'proteins' => 18,
                'fats' => 12,
                'carbs' => 35,
                'fiber' => 2,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'russian',
                'dietary_tags' => ['vegetarian'],
                'allergens' => ['dairy', 'eggs', 'gluten'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Омлет с овощами и сыром',
                'slug' => 'omlet-s-ovoshchami-i-syrom',
                'description' => 'Пышный омлет с болгарским перцем, помидорами и сыром',
                'instructions' => "1. Взбейте яйца с молоком\n2. Обжарьте нарезанные овощи\n3. Залейте яичной смесью\n4. Посыпьте тёртым сыром\n5. Готовьте под крышкой до готовности",
                'prep_time' => 10,
                'cook_time' => 10,
                'servings' => 1,
                'calories' => 300,
                'proteins' => 20,
                'fats' => 22,
                'carbs' => 6,
                'fiber' => 2,
                'difficulty' => 'easy',
                'category' => 'breakfast',
                'cuisine' => 'european',
                'dietary_tags' => ['low-carb', 'keto', 'gluten-free'],
                'allergens' => ['eggs', 'dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Зимние супы и обеды
        $lunches = [
            [
                'title' => 'Щи из квашеной капусты',
                'slug' => 'shchi-iz-kvashenoj-kapusty',
                'description' => 'Традиционные русские щи с квашеной капустой и говядиной',
                'instructions' => "1. Сварите мясной бульон из говядины\n2. Добавьте картофель и варите 15 минут\n3. Добавьте квашеную капусту\n4. Обжарьте лук и морковь, добавьте в суп\n5. Варите ещё 20 минут\n6. Подавайте со сметаной и зеленью",
                'prep_time' => 20,
                'cook_time' => 90,
                'servings' => 6,
                'calories' => 180,
                'proteins' => 14,
                'fats' => 8,
                'carbs' => 12,
                'fiber' => 3,
                'difficulty' => 'medium',
                'category' => 'lunch',
                'cuisine' => 'russian',
                'dietary_tags' => [],
                'allergens' => ['dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Солянка мясная сборная',
                'slug' => 'solyanka-myasnaya-sbornaya',
                'description' => 'Наваристая солянка с несколькими видами мяса и копчёностей',
                'instructions' => "1. Сварите бульон из говядины\n2. Нарежьте колбасу, ветчину, копчёности\n3. Обжарьте лук с томатной пастой\n4. Добавьте солёные огурцы и каперсы\n5. Соедините всё в бульоне\n6. Подавайте с лимоном, маслинами и сметаной",
                'prep_time' => 30,
                'cook_time' => 60,
                'servings' => 8,
                'calories' => 250,
                'proteins' => 18,
                'fats' => 16,
                'carbs' => 8,
                'fiber' => 2,
                'difficulty' => 'medium',
                'category' => 'lunch',
                'cuisine' => 'russian',
                'dietary_tags' => [],
                'allergens' => ['dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Тыквенный крем-суп',
                'slug' => 'tykvennyj-krem-sup',
                'description' => 'Нежный крем-суп из запечённой тыквы с имбирём',
                'instructions' => "1. Запеките тыкву в духовке до мягкости\n2. Обжарьте лук и чеснок\n3. Добавьте тыкву и бульон\n4. Пюрируйте блендером\n5. Добавьте сливки и имбирь\n6. Подавайте с тыквенными семечками",
                'prep_time' => 15,
                'cook_time' => 45,
                'servings' => 4,
                'calories' => 160,
                'proteins' => 4,
                'fats' => 8,
                'carbs' => 20,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'lunch',
                'cuisine' => 'european',
                'dietary_tags' => ['vegetarian', 'gluten-free'],
                'allergens' => ['dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Плов с курицей',
                'slug' => 'plov-s-kuritsej',
                'description' => 'Ароматный плов с куриным филе и специями',
                'instructions' => "1. Обжарьте курицу до золотистой корочки\n2. Добавьте лук и морковь\n3. Всыпьте рис и залейте водой\n4. Добавьте специи: зиру, барбарис, куркуму\n5. Готовьте под крышкой до готовности риса\n6. Дайте настояться 10 минут",
                'prep_time' => 20,
                'cook_time' => 45,
                'servings' => 4,
                'calories' => 380,
                'proteins' => 25,
                'fats' => 12,
                'carbs' => 45,
                'fiber' => 2,
                'difficulty' => 'medium',
                'category' => 'lunch',
                'cuisine' => 'asian',
                'dietary_tags' => ['gluten-free'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Лёгкие ужины
        $dinners = [
            [
                'title' => 'Салат с тунцом и авокадо',
                'slug' => 'salat-s-tuntsom-i-avokado',
                'description' => 'Свежий салат с консервированным тунцом и спелым авокадо',
                'instructions' => "1. Нарежьте листья салата\n2. Добавьте нарезанный авокадо\n3. Выложите тунец\n4. Добавьте помидоры черри и огурец\n5. Заправьте оливковым маслом и лимонным соком",
                'prep_time' => 15,
                'cook_time' => 0,
                'servings' => 2,
                'calories' => 320,
                'proteins' => 28,
                'fats' => 20,
                'carbs' => 8,
                'fiber' => 6,
                'difficulty' => 'easy',
                'category' => 'dinner',
                'cuisine' => 'european',
                'dietary_tags' => ['low-carb', 'gluten-free', 'high-protein'],
                'allergens' => ['fish'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Куриная грудка с брокколи',
                'slug' => 'kurinaya-grudka-s-brokkoli',
                'description' => 'Запечённая куриная грудка с брокколи на пару',
                'instructions' => "1. Замаринуйте курицу в специях и оливковом масле\n2. Запекайте 25 минут при 200°C\n3. Приготовьте брокколи на пару 7 минут\n4. Подавайте вместе, полив соусом",
                'prep_time' => 15,
                'cook_time' => 25,
                'servings' => 2,
                'calories' => 280,
                'proteins' => 38,
                'fats' => 10,
                'carbs' => 8,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'dinner',
                'cuisine' => 'european',
                'dietary_tags' => ['low-carb', 'gluten-free', 'high-protein'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Треска с овощами в духовке',
                'slug' => 'treska-s-ovoshchami-v-duhovke',
                'description' => 'Нежное филе трески, запечённое с сезонными овощами',
                'instructions' => "1. Выложите филе трески на противень\n2. Добавьте нарезанные кабачки, перец, лук\n3. Полейте оливковым маслом\n4. Посыпьте травами и специями\n5. Запекайте 20 минут при 190°C",
                'prep_time' => 15,
                'cook_time' => 20,
                'servings' => 2,
                'calories' => 220,
                'proteins' => 32,
                'fats' => 6,
                'carbs' => 10,
                'fiber' => 3,
                'difficulty' => 'easy',
                'category' => 'dinner',
                'cuisine' => 'european',
                'dietary_tags' => ['low-carb', 'gluten-free', 'high-protein'],
                'allergens' => ['fish'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Тёплый салат с говядиной',
                'slug' => 'tyoplyj-salat-s-govyadinoj',
                'description' => 'Сытный тёплый салат с говядиной и овощами гриль',
                'instructions' => "1. Обжарьте стейк до желаемой прожарки\n2. Приготовьте овощи на гриле\n3. Нарежьте мясо полосками\n4. Выложите на листья салата\n5. Заправьте бальзамическим соусом",
                'prep_time' => 15,
                'cook_time' => 20,
                'servings' => 2,
                'calories' => 350,
                'proteins' => 35,
                'fats' => 18,
                'carbs' => 12,
                'fiber' => 4,
                'difficulty' => 'medium',
                'category' => 'dinner',
                'cuisine' => 'european',
                'dietary_tags' => ['low-carb', 'gluten-free', 'high-protein'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Полезные перекусы
        $snacks = [
            [
                'title' => 'Творог с ягодами и мёдом',
                'slug' => 'tvorog-s-yagodami-i-myodom',
                'description' => 'Нежный творог с замороженными ягодами и капелькой мёда',
                'instructions' => "1. Выложите творог в миску\n2. Добавьте размороженные ягоды\n3. Полейте мёдом\n4. По желанию добавьте орехи",
                'prep_time' => 5,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 180,
                'proteins' => 15,
                'fats' => 5,
                'carbs' => 18,
                'fiber' => 2,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'russian',
                'dietary_tags' => ['vegetarian'],
                'allergens' => ['dairy'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Запечённое яблоко с корицей',
                'slug' => 'zapechyonnoe-yabloko-s-koritsej',
                'description' => 'Тёплое запечённое яблоко с корицей и грецкими орехами',
                'instructions' => "1. Удалите сердцевину яблока\n2. Наполните орехами и изюмом\n3. Посыпьте корицей\n4. Запекайте 20 минут при 180°C",
                'prep_time' => 5,
                'cook_time' => 20,
                'servings' => 1,
                'calories' => 150,
                'proteins' => 2,
                'fats' => 6,
                'carbs' => 24,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'european',
                'dietary_tags' => ['vegetarian', 'vegan'],
                'allergens' => ['nuts'],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Овощные чипсы',
                'slug' => 'ovoshchnye-chipsy',
                'description' => 'Хрустящие чипсы из свёклы, моркови и батата',
                'instructions' => "1. Нарежьте овощи тонкими слайсами\n2. Сбрызните оливковым маслом\n3. Посолите и добавьте специи\n4. Запекайте при 180°C до хруста (15-20 минут)",
                'prep_time' => 15,
                'cook_time' => 20,
                'servings' => 2,
                'calories' => 120,
                'proteins' => 2,
                'fats' => 5,
                'carbs' => 18,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'european',
                'dietary_tags' => ['vegetarian', 'vegan', 'gluten-free'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Смузи детокс',
                'slug' => 'smuzi-detoks',
                'description' => 'Зелёный детокс-смузи со шпинатом, огурцом и имбирём',
                'instructions' => "1. Положите в блендер шпинат и огурец\n2. Добавьте яблоко и имбирь\n3. Влейте воду или кокосовую воду\n4. Взбейте до однородности",
                'prep_time' => 5,
                'cook_time' => 0,
                'servings' => 1,
                'calories' => 80,
                'proteins' => 2,
                'fats' => 0,
                'carbs' => 18,
                'fiber' => 4,
                'difficulty' => 'easy',
                'category' => 'snack',
                'cuisine' => 'american',
                'dietary_tags' => ['vegetarian', 'vegan', 'gluten-free'],
                'allergens' => [],
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        // Создаём рецепты (пропускаем существующие по slug)
        foreach (array_merge($breakfasts, $lunches, $dinners, $snacks) as $recipe) {
            Recipe::firstOrCreate(
                ['slug' => $recipe['slug']],
                $recipe
            );
        }

        $this->command->info('Рецепты для января созданы/обновлены');
    }
}
