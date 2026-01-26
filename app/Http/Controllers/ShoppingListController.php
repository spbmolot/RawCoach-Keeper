<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Day;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать страницу списка покупок
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Получаем опубликованные меню
        $menus = Menu::where('is_published', true)
            ->with('days')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        return view('shopping-list.index', compact('menus'));
    }

    /**
     * Показать список покупок для меню
     */
    public function show(Menu $menu, Request $request)
    {
        $startDay = $request->get('start_day', 1);
        $endDay = $request->get('end_day', 7);
        
        $menu->load(['days.meals.recipe.ingredients']);
        
        // Фильтруем дни по диапазону
        $days = $menu->days->filter(function($day) use ($startDay, $endDay) {
            return $day->day_number >= $startDay && $day->day_number <= $endDay;
        });
        
        // Собираем ингредиенты
        $shoppingList = $this->generateShoppingList($days);
        
        return view('shopping-list.show', compact('menu', 'shoppingList', 'startDay', 'endDay'));
    }

    /**
     * Экспорт списка покупок в PDF
     */
    public function exportPdf(Menu $menu, Request $request)
    {
        $startDay = $request->get('start_day', 1);
        $endDay = $request->get('end_day', 7);
        
        $menu->load(['days.meals.recipe.ingredients']);
        
        // Фильтруем дни по диапазону
        $days = $menu->days->filter(function($day) use ($startDay, $endDay) {
            return $day->day_number >= $startDay && $day->day_number <= $endDay;
        });
        
        // Собираем ингредиенты
        $shoppingList = $this->generateShoppingList($days);
        
        $pdf = Pdf::loadView('shopping-list.pdf', [
            'menu' => $menu,
            'shoppingList' => $shoppingList,
            'startDay' => $startDay,
            'endDay' => $endDay,
        ]);
        
        $filename = "shopping-list-{$menu->slug}-days-{$startDay}-{$endDay}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Генерация списка покупок из дней меню
     */
    private function generateShoppingList($days): array
    {
        $ingredients = [];
        
        foreach ($days as $day) {
            foreach ($day->meals as $meal) {
                if (!$meal->recipe) continue;
                
                foreach ($meal->recipe->ingredients as $ingredient) {
                    $key = mb_strtolower($ingredient->ingredient_name);
                    
                    if (!isset($ingredients[$key])) {
                        $ingredients[$key] = [
                            'name' => $ingredient->ingredient_name,
                            'amount' => 0,
                            'unit' => $ingredient->unit,
                            'category' => $ingredient->category ?? 'Прочее',
                        ];
                    }
                    
                    $ingredients[$key]['amount'] += $ingredient->amount;
                }
            }
        }
        
        // Группируем по категориям
        $grouped = [];
        foreach ($ingredients as $ingredient) {
            $category = $ingredient['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $ingredient;
        }
        
        // Сортируем категории
        ksort($grouped);
        
        return $grouped;
    }
}
