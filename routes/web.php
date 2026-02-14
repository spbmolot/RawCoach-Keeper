<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\PersonalPlanController;
use App\Http\Controllers\AdCampaignController;
use App\Http\Controllers\Webhook\YooKassaWebhookController;
use App\Http\Controllers\Webhook\CloudPaymentsWebhookController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TestPaymentController;
use App\Http\Controllers\DiagnosticsController;

// Публичные страницы
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit')->middleware(['throttle:contact', 'recaptcha:contact']);
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/offer', [HomeController::class, 'offer'])->name('offer');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Диагностика системы (защищена ключом)
Route::get('/diagnostics', [DiagnosticsController::class, 'index'])->name('diagnostics');

// Тестовые маршруты для платежей (только в dev)
if (app()->environment('local', 'development', 'testing')) {
    Route::prefix('payment/test')->name('payment.test.')->group(function () {
        Route::get('/confirm', [TestPaymentController::class, 'confirm'])->name('confirm');
        Route::get('/success', [TestPaymentController::class, 'success'])->name('success');
        Route::get('/cancel', [TestPaymentController::class, 'cancel'])->name('cancel');
    });
}

// Планы подписки (доступны всем)
Route::prefix('plans')->name('plans.')->group(function () {
    Route::get('/', [PlansController::class, 'index'])->name('index');
    Route::get('/compare', [PlansController::class, 'compare'])->name('compare');
    Route::get('/choose/{plan}', [PlanController::class, 'choose'])->name('choose');
    Route::get('/{plan}', [PlansController::class, 'show'])->name('show');
});

// Вебхуки платежных систем (без CSRF, с rate limiting)
Route::post('/webhook/yookassa', YooKassaWebhookController::class)->name('webhook.yookassa')->middleware('throttle:webhooks');
Route::post('/webhook/cloudpayments', CloudPaymentsWebhookController::class)->name('webhook.cloudpayments')->middleware('throttle:webhooks');

// Редирект с Jetstream профиля на наш кастомный
Route::redirect('/user/profile', '/dashboard/profile');

// Авторизованные пользователи
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    // Личный кабинет
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/home', [DashboardController::class, 'index'])->name('index');
        Route::get('/today', [DashboardController::class, 'today'])->name('today');
        Route::get('/week', [DashboardController::class, 'week'])->name('week');
        Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
        Route::get('/shopping-list', [DashboardController::class, 'shoppingList'])->name('shopping-list');
        Route::post('/shopping-list/export', [DashboardController::class, 'exportShoppingList'])->name('shopping-list.export');
        Route::get('/personal-plans', [DashboardController::class, 'personalPlans'])->name('personal-plans');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // Подписки (только для авторизованных)
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/{plan}', [SubscriptionController::class, 'create'])->name('create');
        Route::patch('/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::patch('/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('pause');
        Route::patch('/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::patch('/{subscription}/toggle-renewal', [SubscriptionController::class, 'toggleRenewal'])->name('toggle-renewal');
        Route::post('/upgrade/{plan}', [SubscriptionController::class, 'upgrade'])->name('upgrade');
    });
    
    // Планы (дополнительные маршруты для авторизованных)
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/upgrade', [PlansController::class, 'upgrade'])->name('upgrade');
    });
    
    // Платежи
    Route::prefix('payments')->name('payment.')->group(function () {
        Route::get('/{payment}/process', [PaymentController::class, 'process'])->name('process');
        Route::post('/{payment}/create', [PaymentController::class, 'create'])->name('create');
        Route::get('/success', [PaymentController::class, 'success'])->name('success');
        Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
    });
    
    // Меню (только для авторизованных)
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/search', [MenuController::class, 'search'])->name('search');
        Route::get('/archive', [MenuController::class, 'archive'])->name('archive');
        Route::get('/early', [MenuController::class, 'early'])->name('early');
        Route::get('/favorites', [MenuController::class, 'favorites'])->name('favorites');
        Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
        Route::post('/{menu}/favorite', [MenuController::class, 'favorite'])->name('favorite');
        Route::get('/{menu}/export', [MenuController::class, 'export'])->name('export');
    });
    
    // Рецепты (только для авторизованных)
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');
        Route::get('/search', [RecipeController::class, 'search'])->name('search');
        Route::get('/popular', [RecipeController::class, 'popular'])->name('popular');
        Route::get('/favorites', [RecipeController::class, 'favorites'])->name('favorites');
        Route::get('/meal/{mealType}', [RecipeController::class, 'byMealType'])->name('by-meal-type');
        Route::get('/{recipe}', [RecipeController::class, 'show'])->name('show');
        Route::post('/{recipe}/favorite', [RecipeController::class, 'favorite'])->name('favorite');
        Route::get('/{recipe}/export', [RecipeController::class, 'export'])->name('export');
    });
    
    // Демо-страница (только для авторизованных)
    Route::get('/demo', [HomeController::class, 'demo'])->name('demo');
    
    // Список покупок
    Route::prefix('shopping-list')->name('shopping-list.')->group(function () {
        Route::get('/', [ShoppingListController::class, 'index'])->name('index');
        Route::get('/{menu}', [ShoppingListController::class, 'show'])->name('show');
        Route::get('/{menu}/pdf', [ShoppingListController::class, 'exportPdf'])->name('pdf');
    });
    
    // Персональные планы
    Route::prefix('personal-plans')->name('personal-plans.')->group(function () {
        Route::get('/', [PersonalPlanController::class, 'index'])->name('index');
        Route::get('/create', [PersonalPlanController::class, 'create'])->name('create');
        Route::post('/', [PersonalPlanController::class, 'store'])->name('store');
        Route::get('/{personalPlan}', [PersonalPlanController::class, 'show'])->name('show');
        Route::get('/{personalPlan}/edit', [PersonalPlanController::class, 'edit'])->name('edit');
        Route::put('/{personalPlan}', [PersonalPlanController::class, 'update'])->name('update');
        Route::delete('/{personalPlan}', [PersonalPlanController::class, 'cancel'])->name('cancel');
        Route::post('/{personalPlan}/rate', [PersonalPlanController::class, 'rate'])->name('rate');
        Route::get('/{personalPlan}/download', [PersonalPlanController::class, 'download'])->name('download');
        Route::get('/{personalPlan}/chat', [PersonalPlanController::class, 'chat'])->name('chat');
        Route::post('/{personalPlan}/chat', [PersonalPlanController::class, 'sendMessage'])->name('chat.send');
    });
    
    // Рекламные кампании (только для рекламодателей и админов)
    Route::prefix('ad-campaigns')->name('ad-campaigns.')->middleware(['role:advertiser|admin'])->group(function () {
        Route::get('/', [AdCampaignController::class, 'index'])->name('index');
        Route::get('/create', [AdCampaignController::class, 'create'])->name('create');
        Route::post('/', [AdCampaignController::class, 'store'])->name('store');
        Route::get('/{adCampaign}', [AdCampaignController::class, 'show'])->name('show');
        Route::get('/{adCampaign}/edit', [AdCampaignController::class, 'edit'])->name('edit');
        Route::put('/{adCampaign}', [AdCampaignController::class, 'update'])->name('update');
        Route::delete('/{adCampaign}', [AdCampaignController::class, 'destroy'])->name('destroy');
        Route::patch('/{adCampaign}/pause', [AdCampaignController::class, 'pause'])->name('pause');
        Route::patch('/{adCampaign}/resume', [AdCampaignController::class, 'resume'])->name('resume');
        Route::get('/{adCampaign}/stats', [AdCampaignController::class, 'stats'])->name('stats');
        Route::get('/{adCampaign}/creatives', [AdCampaignController::class, 'creatives'])->name('creatives');
        Route::post('/{adCampaign}/creatives', [AdCampaignController::class, 'storeCreative'])->name('creatives.store');
    });
});
