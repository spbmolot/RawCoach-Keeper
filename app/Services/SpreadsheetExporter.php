<?php

namespace App\Services;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetExporter
{
    /**
     * Экспорт рецепта в XLSX
     */
    public static function exportRecipe($recipe, string $filename): StreamedResponse
    {
        return new StreamedResponse(function () use ($recipe) {
            $options = new Options();
            $writer = new Writer($options);
            $writer->openToOutput();

            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontSize(12)
                ->setBackgroundColor('10B981')
                ->setFontColor(Color::WHITE);

            $subHeaderStyle = (new Style())
                ->setFontBold()
                ->setBackgroundColor('ECFDF5');

            // --- Лист 1: Рецепт ---
            $writer->getCurrentSheet()->setName('Рецепт');

            // Заголовок
            $writer->addRow(Row::fromValues([$recipe->title], $headerStyle));
            $writer->addRow(Row::fromValues([]));

            // Мета-информация
            $writer->addRow(Row::fromValues(['Параметр', 'Значение'], $subHeaderStyle));
            $writer->addRow(Row::fromValues(['Категория', $recipe->getCategoryName()]));
            $writer->addRow(Row::fromValues(['Сложность', $recipe->getDifficultyName()]));
            $writer->addRow(Row::fromValues(['Подготовка (мин)', $recipe->prep_time]));
            $writer->addRow(Row::fromValues(['Готовка (мин)', $recipe->cook_time]));
            $writer->addRow(Row::fromValues(['Общее время (мин)', $recipe->prep_time + $recipe->cook_time]));
            $writer->addRow(Row::fromValues(['Порций', $recipe->servings]));
            $writer->addRow(Row::fromValues([]));

            // КБЖУ
            $writer->addRow(Row::fromValues(['Пищевая ценность (на порцию)'], $subHeaderStyle));
            $writer->addRow(Row::fromValues(['Калории', round($recipe->calories) . ' ккал']));
            $writer->addRow(Row::fromValues(['Белки', $recipe->proteins . ' г']));
            $writer->addRow(Row::fromValues(['Жиры', $recipe->fats . ' г']));
            $writer->addRow(Row::fromValues(['Углеводы', $recipe->carbs . ' г']));
            if ($recipe->fiber) {
                $writer->addRow(Row::fromValues(['Клетчатка', $recipe->fiber . ' г']));
            }
            $writer->addRow(Row::fromValues([]));

            // Ингредиенты
            $writer->addRow(Row::fromValues(['Ингредиент', 'Количество', 'Единица', 'Примечание'], $subHeaderStyle));
            foreach ($recipe->ingredients as $ing) {
                $writer->addRow(Row::fromValues([
                    $ing->ingredient_name,
                    $ing->amount ? round($ing->amount, 1) : '',
                    $ing->unit ?? '',
                    ($ing->is_optional ? '(по желанию) ' : '') . ($ing->preparation_notes ?? ''),
                ]));
            }
            $writer->addRow(Row::fromValues([]));

            // Приготовление
            $writer->addRow(Row::fromValues(['Приготовление'], $subHeaderStyle));
            if ($recipe->instructions) {
                $steps = is_array($recipe->instructions)
                    ? $recipe->instructions
                    : preg_split('/\r?\n/', $recipe->instructions);
                $steps = array_values(array_filter($steps, fn($s) => trim($s) !== ''));
                foreach ($steps as $i => $step) {
                    $writer->addRow(Row::fromValues([($i + 1) . '. ' . trim($step)]));
                }
            }

            if ($recipe->notes) {
                $writer->addRow(Row::fromValues([]));
                $writer->addRow(Row::fromValues(['Заметки'], $subHeaderStyle));
                $writer->addRow(Row::fromValues([$recipe->notes]));
            }

            $writer->close();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Экспорт меню в XLSX
     */
    public static function exportMenu($menu, $days, string $filename): StreamedResponse
    {
        return new StreamedResponse(function () use ($menu, $days) {
            $options = new Options();
            $writer = new Writer($options);
            $writer->openToOutput();

            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontSize(12)
                ->setBackgroundColor('10B981')
                ->setFontColor(Color::WHITE);

            $subHeaderStyle = (new Style())
                ->setFontBold()
                ->setBackgroundColor('ECFDF5');

            $mealTypeNames = [
                'breakfast' => 'Завтрак',
                'lunch' => 'Обед',
                'dinner' => 'Ужин',
                'snack' => 'Перекус',
            ];

            // --- Лист 1: Обзор меню ---
            $writer->getCurrentSheet()->setName('Меню');

            $writer->addRow(Row::fromValues([$menu->title ?? $menu->name ?? 'Меню'], $headerStyle));
            if ($menu->description) {
                $writer->addRow(Row::fromValues([$menu->description]));
            }
            $writer->addRow(Row::fromValues([]));

            // Таблица дней
            $writer->addRow(Row::fromValues([
                'День', 'Завтрак', 'Обед', 'Ужин', 'Перекус', 'Ккал', 'Б', 'Ж', 'У'
            ], $subHeaderStyle));

            foreach ($days as $day) {
                $meals = $day->meals->groupBy('meal_type');
                $row = [
                    'День ' . $day->day_number,
                    self::getMealNames($meals, 'breakfast'),
                    self::getMealNames($meals, 'lunch'),
                    self::getMealNames($meals, 'dinner'),
                    self::getMealNames($meals, 'snack'),
                    round($day->total_calories ?? 0),
                    round($day->total_proteins ?? 0, 1),
                    round($day->total_fats ?? 0, 1),
                    round($day->total_carbs ?? 0, 1),
                ];
                $writer->addRow(Row::fromValues($row));
            }

            // --- Лист 2: Детальные рецепты ---
            $sheet2 = $writer->addNewSheetAndMakeItCurrent();
            $sheet2->setName('Рецепты');

            foreach ($days as $day) {
                $writer->addRow(Row::fromValues(
                    ['День ' . $day->day_number . ' — ' . $day->getDayOfWeekName()],
                    $headerStyle
                ));

                foreach ($day->meals as $meal) {
                    if (!$meal->recipe) continue;

                    $recipe = $meal->recipe;
                    $mealLabel = $mealTypeNames[$meal->meal_type] ?? $meal->meal_type;

                    $writer->addRow(Row::fromValues(
                        ["{$mealLabel}: {$recipe->title}"],
                        $subHeaderStyle
                    ));

                    $writer->addRow(Row::fromValues([
                        'Ккал: ' . round($recipe->calories),
                        'Б: ' . $recipe->proteins . 'г',
                        'Ж: ' . $recipe->fats . 'г',
                        'У: ' . $recipe->carbs . 'г',
                        'Время: ' . ($recipe->prep_time + $recipe->cook_time) . ' мин',
                    ]));

                    if ($recipe->ingredients && $recipe->ingredients->count() > 0) {
                        foreach ($recipe->ingredients as $ing) {
                            $amount = $ing->amount ? round($ing->amount, 1) . ' ' . ($ing->unit ?? '') : '';
                            $writer->addRow(Row::fromValues([
                                '  • ' . $ing->ingredient_name, $amount
                            ]));
                        }
                    }

                    $writer->addRow(Row::fromValues([]));
                }
            }

            // --- Лист 3: Список покупок ---
            $sheet3 = $writer->addNewSheetAndMakeItCurrent();
            $sheet3->setName('Список покупок');

            $writer->addRow(Row::fromValues(['Список покупок'], $headerStyle));
            $writer->addRow(Row::fromValues([]));

            $allIngredients = self::aggregateIngredients($days);

            $currentCategory = null;
            foreach ($allIngredients as $item) {
                if ($item['category'] !== $currentCategory) {
                    $currentCategory = $item['category'];
                    $writer->addRow(Row::fromValues([$currentCategory], $subHeaderStyle));
                }
                $writer->addRow(Row::fromValues([
                    $item['name'],
                    round($item['amount'], 1),
                    $item['unit'],
                ]));
            }

            $writer->close();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Получить названия рецептов для типа приёма пищи
     */
    private static function getMealNames($groupedMeals, string $type): string
    {
        if (!isset($groupedMeals[$type])) {
            return '—';
        }

        return $groupedMeals[$type]
            ->map(fn($m) => $m->recipe?->title ?? '—')
            ->implode(', ');
    }

    /**
     * Агрегировать ингредиенты из всех дней
     */
    private static function aggregateIngredients($days): array
    {
        $ingredients = [];

        foreach ($days as $day) {
            foreach ($day->meals as $meal) {
                if (!$meal->recipe || !$meal->recipe->ingredients) continue;

                foreach ($meal->recipe->ingredients as $ing) {
                    $key = mb_strtolower($ing->ingredient_name) . '_' . ($ing->unit ?? '');

                    if (!isset($ingredients[$key])) {
                        $ingredients[$key] = [
                            'name' => $ing->ingredient_name,
                            'amount' => 0,
                            'unit' => $ing->unit ?? '',
                            'category' => $ing->getCategoryName(),
                        ];
                    }
                    $ingredients[$key]['amount'] += $ing->amount;
                }
            }
        }

        // Сортируем по категории, потом по имени
        usort($ingredients, function ($a, $b) {
            $cmp = strcmp($a['category'], $b['category']);
            return $cmp !== 0 ? $cmp : strcmp($a['name'], $b['name']);
        });

        return $ingredients;
    }
}
