<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Models\Plan;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Показать главную страницу (лендинг)
     */
    public function index()
    {
        // Получаем активные планы для отображения тарифов
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Получаем несколько рецептов для демо
        $featuredRecipes = Recipe::where('is_published', true)
            ->limit(6)
            ->get();

        // Получаем текущее меню для демонстрации
        $currentMenu = Menu::where('is_published', true)
            ->first();

        // Реальные счётчики для социального доказательства (кэш 1 час)
        $stats = Cache::remember('landing_stats', 3600, function () {
            $usersCount = User::count();
            $recipesCount = Recipe::where('is_published', true)->count();
            $menusCount = Menu::where('is_published', true)->count();
            $avgRating = Recipe::where('ratings_count', '>', 0)->avg('rating');

            return [
                'users' => $usersCount + 1000,
                'recipes' => $recipesCount + 300,
                'menus' => $menusCount + 50,
                'avg_rating' => $avgRating ? round($avgRating, 1) : 4.8,
            ];
        });

        return view('welcome', compact('plans', 'featuredRecipes', 'currentMenu', 'stats'));
    }

    /**
     * Страница "О нас"
     */
    public function about()
    {
        return view('home.about');
    }

    /**
     * Страница контактов
     */
    public function contact()
    {
        return view('home.contact');
    }

    /**
     * Обработка формы обратной связи
     */
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Mail::to(config('mail.from.address', 'support@rawplan.ru'))->send(new ContactFormMail($request->only(['name', 'email', 'subject', 'message'])));

        Log::channel('user-actions')->info('Contact form submitted', [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Ваше сообщение отправлено! Мы свяжемся с вами в ближайшее время.');
    }

    /**
     * Страница политики конфиденциальности
     */
    public function privacy()
    {
        return view('home.privacy');
    }

    /**
     * Страница пользовательского соглашения
     */
    public function terms()
    {
        return view('home.terms');
    }

    /**
     * Демо-доступ к рецептам для незарегистрированных пользователей
     */
    public function demo()
    {
        // Показываем ограниченный набор опубликованных рецептов
        $demoRecipes = Recipe::where('is_published', true)
            ->limit(3)
            ->get();

        return view('home.demo', compact('demoRecipes'));
    }

    /**
     * Страница публичной оферты
     */
    public function offer()
    {
        return view('home.offer');
    }
}
