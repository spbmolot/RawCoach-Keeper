<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Database\Seeder;

class MissingRecipeIngredientsSeeder extends Seeder
{
    public function run(): void
    {
        $recipesIngredients = [
            'Овсянка с ягодами и орехами' => [
                ['name' => 'Овсяные хлопья', 'amount' => 60, 'unit' => 'г', 'category' => 'Крупы'],
                ['name' => 'Молоко 2.5%', 'amount' => 150, 'unit' => 'мл', 'category' => 'Молочные'],
                ['name' => 'Ягоды (малина, черника)', 'amount' => 50, 'unit' => 'г', 'category' => 'Фрукты'],
                ['name' => 'Грецкие орехи', 'amount' => 20, 'unit' => 'г', 'category' => 'Орехи'],
                ['name' => 'Мёд', 'amount' => 10, 'unit' => 'г', 'category' => 'Прочее', 'is_optional' => true],
            ],
            'Гречневая каша с грибами' => [
                ['name' => 'Гречневая крупа', 'amount' => 100, 'unit' => 'г', 'category' => 'Крупы'],
                ['name' => 'Шампиньоны', 'amount' => 150, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Лук репчатый', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Масло сливочное', 'amount' => 15, 'unit' => 'г', 'category' => 'Молочные'],
                ['name' => 'Соль, перец', 'amount' => 3, 'unit' => 'г', 'category' => 'Специи'],
                ['name' => 'Укроп', 'amount' => 10, 'unit' => 'г', 'category' => 'Зелень'],
            ],
            'Смузи-боул с бананом и шпинатом' => [
                ['name' => 'Банан замороженный', 'amount' => 1, 'unit' => 'шт', 'category' => 'Фрукты'],
                ['name' => 'Шпинат', 'amount' => 30, 'unit' => 'г', 'category' => 'Зелень'],
                ['name' => 'Молоко миндальное', 'amount' => 150, 'unit' => 'мл', 'category' => 'Молочные'],
                ['name' => 'Гранола', 'amount' => 30, 'unit' => 'г', 'category' => 'Крупы'],
                ['name' => 'Ягоды свежие', 'amount' => 30, 'unit' => 'г', 'category' => 'Фрукты'],
                ['name' => 'Семена чиа', 'amount' => 5, 'unit' => 'г', 'category' => 'Орехи'],
            ],
            'Сырники с ягодным соусом' => [
                ['name' => 'Творог 5%', 'amount' => 250, 'unit' => 'г', 'category' => 'Молочные'],
                ['name' => 'Яйцо куриное', 'amount' => 1, 'unit' => 'шт', 'category' => 'Яйца'],
                ['name' => 'Мука пшеничная', 'amount' => 30, 'unit' => 'г', 'category' => 'Крупы'],
                ['name' => 'Сахар', 'amount' => 15, 'unit' => 'г', 'category' => 'Прочее'],
                ['name' => 'Ягоды (клубника, малина)', 'amount' => 100, 'unit' => 'г', 'category' => 'Фрукты'],
                ['name' => 'Масло для жарки', 'amount' => 15, 'unit' => 'мл', 'category' => 'Масла'],
            ],
            'Омлет с овощами и сыром' => [
                ['name' => 'Яйца куриные', 'amount' => 3, 'unit' => 'шт', 'category' => 'Яйца'],
                ['name' => 'Молоко 2.5%', 'amount' => 50, 'unit' => 'мл', 'category' => 'Молочные'],
                ['name' => 'Помидор', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Болгарский перец', 'amount' => 0.5, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Сыр твёрдый', 'amount' => 30, 'unit' => 'г', 'category' => 'Молочные'],
                ['name' => 'Масло сливочное', 'amount' => 10, 'unit' => 'г', 'category' => 'Молочные'],
                ['name' => 'Зелень', 'amount' => 10, 'unit' => 'г', 'category' => 'Зелень'],
            ],
            'Щи из квашеной капусты' => [
                ['name' => 'Капуста квашеная', 'amount' => 200, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Говядина', 'amount' => 150, 'unit' => 'г', 'category' => 'Мясо'],
                ['name' => 'Картофель', 'amount' => 2, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Морковь', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Лук репчатый', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Томатная паста', 'amount' => 20, 'unit' => 'г', 'category' => 'Соусы'],
                ['name' => 'Лавровый лист', 'amount' => 2, 'unit' => 'шт', 'category' => 'Специи'],
                ['name' => 'Сметана', 'amount' => 30, 'unit' => 'г', 'category' => 'Молочные'],
            ],
            'Солянка мясная сборная' => [
                ['name' => 'Говядина', 'amount' => 100, 'unit' => 'г', 'category' => 'Мясо'],
                ['name' => 'Колбаса варёная', 'amount' => 50, 'unit' => 'г', 'category' => 'Мясо'],
                ['name' => 'Сосиски', 'amount' => 2, 'unit' => 'шт', 'category' => 'Мясо'],
                ['name' => 'Огурцы солёные', 'amount' => 3, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Лук репчатый', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Маслины', 'amount' => 50, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Томатная паста', 'amount' => 30, 'unit' => 'г', 'category' => 'Соусы'],
                ['name' => 'Лимон', 'amount' => 0.5, 'unit' => 'шт', 'category' => 'Фрукты'],
                ['name' => 'Сметана', 'amount' => 30, 'unit' => 'г', 'category' => 'Молочные'],
            ],
            'Тыквенный крем-суп' => [
                ['name' => 'Тыква', 'amount' => 400, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Картофель', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Лук репчатый', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Сливки 20%', 'amount' => 100, 'unit' => 'мл', 'category' => 'Молочные'],
                ['name' => 'Бульон овощной', 'amount' => 500, 'unit' => 'мл', 'category' => 'Прочее'],
                ['name' => 'Тыквенные семечки', 'amount' => 20, 'unit' => 'г', 'category' => 'Орехи'],
                ['name' => 'Мускатный орех', 'amount' => 2, 'unit' => 'г', 'category' => 'Специи'],
            ],
            'Плов с курицей' => [
                ['name' => 'Рис длиннозёрный', 'amount' => 150, 'unit' => 'г', 'category' => 'Крупы'],
                ['name' => 'Куриное филе', 'amount' => 200, 'unit' => 'г', 'category' => 'Мясо'],
                ['name' => 'Морковь', 'amount' => 2, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Лук репчатый', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Чеснок', 'amount' => 1, 'unit' => 'головка', 'category' => 'Овощи'],
                ['name' => 'Масло растительное', 'amount' => 30, 'unit' => 'мл', 'category' => 'Масла'],
                ['name' => 'Зира', 'amount' => 5, 'unit' => 'г', 'category' => 'Специи'],
                ['name' => 'Барбарис', 'amount' => 5, 'unit' => 'г', 'category' => 'Специи'],
            ],
            'Салат с тунцом и авокадо' => [
                ['name' => 'Тунец консервированный', 'amount' => 100, 'unit' => 'г', 'category' => 'Рыба'],
                ['name' => 'Авокадо', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Огурец', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Помидоры черри', 'amount' => 100, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Листья салата', 'amount' => 50, 'unit' => 'г', 'category' => 'Зелень'],
                ['name' => 'Масло оливковое', 'amount' => 15, 'unit' => 'мл', 'category' => 'Масла'],
                ['name' => 'Лимонный сок', 'amount' => 10, 'unit' => 'мл', 'category' => 'Прочее'],
            ],
            'Куриная грудка с брокколи' => [
                ['name' => 'Куриная грудка', 'amount' => 180, 'unit' => 'г', 'category' => 'Мясо'],
                ['name' => 'Брокколи', 'amount' => 200, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Чеснок', 'amount' => 2, 'unit' => 'зубчика', 'category' => 'Овощи'],
                ['name' => 'Соевый соус', 'amount' => 20, 'unit' => 'мл', 'category' => 'Соусы'],
                ['name' => 'Масло оливковое', 'amount' => 15, 'unit' => 'мл', 'category' => 'Масла'],
                ['name' => 'Кунжут', 'amount' => 5, 'unit' => 'г', 'category' => 'Орехи'],
            ],
            'Треска с овощами в духовке' => [
                ['name' => 'Треска (филе)', 'amount' => 200, 'unit' => 'г', 'category' => 'Рыба'],
                ['name' => 'Кабачок', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Помидор', 'amount' => 2, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Лук репчатый', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Лимон', 'amount' => 0.5, 'unit' => 'шт', 'category' => 'Фрукты'],
                ['name' => 'Масло оливковое', 'amount' => 20, 'unit' => 'мл', 'category' => 'Масла'],
                ['name' => 'Прованские травы', 'amount' => 5, 'unit' => 'г', 'category' => 'Специи'],
            ],
            'Тёплый салат с говядиной' => [
                ['name' => 'Говядина (вырезка)', 'amount' => 150, 'unit' => 'г', 'category' => 'Мясо'],
                ['name' => 'Листья салата микс', 'amount' => 100, 'unit' => 'г', 'category' => 'Зелень'],
                ['name' => 'Помидоры черри', 'amount' => 100, 'unit' => 'г', 'category' => 'Овощи'],
                ['name' => 'Огурец', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Болгарский перец', 'amount' => 0.5, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Масло оливковое', 'amount' => 20, 'unit' => 'мл', 'category' => 'Масла'],
                ['name' => 'Бальзамический уксус', 'amount' => 10, 'unit' => 'мл', 'category' => 'Соусы'],
            ],
            'Творог с ягодами и мёдом' => [
                ['name' => 'Творог 5%', 'amount' => 200, 'unit' => 'г', 'category' => 'Молочные'],
                ['name' => 'Ягоды свежие (клубника, черника)', 'amount' => 80, 'unit' => 'г', 'category' => 'Фрукты'],
                ['name' => 'Мёд', 'amount' => 15, 'unit' => 'г', 'category' => 'Прочее'],
                ['name' => 'Мята', 'amount' => 3, 'unit' => 'г', 'category' => 'Зелень', 'is_optional' => true],
            ],
            'Запечённое яблоко с корицей' => [
                ['name' => 'Яблоко', 'amount' => 2, 'unit' => 'шт', 'category' => 'Фрукты'],
                ['name' => 'Мёд', 'amount' => 20, 'unit' => 'г', 'category' => 'Прочее'],
                ['name' => 'Корица', 'amount' => 3, 'unit' => 'г', 'category' => 'Специи'],
                ['name' => 'Грецкие орехи', 'amount' => 20, 'unit' => 'г', 'category' => 'Орехи'],
                ['name' => 'Изюм', 'amount' => 15, 'unit' => 'г', 'category' => 'Сухофрукты'],
            ],
            'Овощные чипсы' => [
                ['name' => 'Свёкла', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Морковь', 'amount' => 2, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Батат', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Масло оливковое', 'amount' => 15, 'unit' => 'мл', 'category' => 'Масла'],
                ['name' => 'Соль морская', 'amount' => 3, 'unit' => 'г', 'category' => 'Специи'],
                ['name' => 'Паприка', 'amount' => 2, 'unit' => 'г', 'category' => 'Специи'],
            ],
            'Смузи детокс' => [
                ['name' => 'Огурец', 'amount' => 1, 'unit' => 'шт', 'category' => 'Овощи'],
                ['name' => 'Сельдерей', 'amount' => 2, 'unit' => 'стебля', 'category' => 'Овощи'],
                ['name' => 'Яблоко зелёное', 'amount' => 1, 'unit' => 'шт', 'category' => 'Фрукты'],
                ['name' => 'Шпинат', 'amount' => 30, 'unit' => 'г', 'category' => 'Зелень'],
                ['name' => 'Лимонный сок', 'amount' => 15, 'unit' => 'мл', 'category' => 'Прочее'],
                ['name' => 'Имбирь свежий', 'amount' => 5, 'unit' => 'г', 'category' => 'Специи'],
                ['name' => 'Вода', 'amount' => 150, 'unit' => 'мл', 'category' => 'Прочее'],
            ],
        ];

        foreach ($recipesIngredients as $recipeTitle => $ingredients) {
            $recipe = Recipe::where('title', $recipeTitle)->first();
            
            if (!$recipe) {
                $this->command->warn("Рецепт не найден: {$recipeTitle}");
                continue;
            }

            // Проверяем, есть ли уже ингредиенты
            if ($recipe->ingredients()->count() > 0) {
                $this->command->info("Рецепт уже имеет ингредиенты: {$recipeTitle}");
                continue;
            }

            $order = 1;
            foreach ($ingredients as $ingredient) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_name' => $ingredient['name'],
                    'amount' => $ingredient['amount'],
                    'unit' => $ingredient['unit'],
                    'category' => $ingredient['category'],
                    'is_optional' => $ingredient['is_optional'] ?? false,
                    'order' => $order++,
                ]);
            }

            $this->command->info("Добавлены ингредиенты для: {$recipeTitle}");
        }

        $this->command->info('Сидер завершён. Добавлены ингредиенты к рецептам.');
    }
}
