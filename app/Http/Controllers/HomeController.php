<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Menu;
use App\Models\Recipe;

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

        // Получаем несколько популярных рецептов для демо
        $featuredRecipes = Recipe::where('is_published', true)
            ->where('is_featured', true)
            ->limit(6)
            ->get();

        // Получаем текущее меню для демонстрации
        $currentMenu = Menu::where('is_published', true)
            ->where('type', 'current')
            ->first();

        return view('home.index', compact('plans', 'featuredRecipes', 'currentMenu'));
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

        // Здесь можно отправить email или сохранить в БД
        // Mail::to('support@rawplan.ru')->send(new ContactFormMail($request->all()));

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
        // Показываем ограниченный набор рецептов
        $demoRecipes = Recipe::where('is_published', true)
            ->where('is_demo', true)
            ->limit(3)
            ->get();

        return view('home.demo', compact('demoRecipes'));
    }
}
