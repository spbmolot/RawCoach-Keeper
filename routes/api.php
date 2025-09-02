<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PersonalPlanController;

// Публичные API маршруты
Route::prefix('v1')->group(function () {
    
    // Аутентификация
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
    
    // Публичные данные
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{menu}', [MenuController::class, 'show']);
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
    Route::get('/recipes/search', [RecipeController::class, 'search']);
    
    // Защищенные маршруты
    Route::middleware('auth:sanctum')->group(function () {
        
        // Пользователь
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Личный кабинет
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/today', [DashboardController::class, 'today']);
            Route::get('/week', [DashboardController::class, 'week']);
            Route::get('/calendar', [DashboardController::class, 'calendar']);
            Route::get('/shopping-list', [DashboardController::class, 'shoppingList']);
            Route::get('/profile', [DashboardController::class, 'profile']);
            Route::put('/profile', [DashboardController::class, 'updateProfile']);
        });
        
        // Подписки
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index']);
            Route::post('/{plan}', [SubscriptionController::class, 'create']);
            Route::patch('/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
            Route::patch('/{subscription}/pause', [SubscriptionController::class, 'pause']);
            Route::patch('/{subscription}/resume', [SubscriptionController::class, 'resume']);
        });
        
        // Меню (с подпиской)
        Route::prefix('menus')->group(function () {
            Route::get('/archive', [MenuController::class, 'archive']);
            Route::get('/early', [MenuController::class, 'early']);
            Route::get('/favorites', [MenuController::class, 'favorites']);
            Route::post('/{menu}/favorite', [MenuController::class, 'favorite']);
        });
        
        // Рецепты (с подпиской)
        Route::prefix('recipes')->group(function () {
            Route::get('/favorites', [RecipeController::class, 'favorites']);
            Route::post('/{recipe}/favorite', [RecipeController::class, 'favorite']);
        });
        
        // Персональные планы
        Route::prefix('personal-plans')->group(function () {
            Route::get('/', [PersonalPlanController::class, 'index']);
            Route::post('/', [PersonalPlanController::class, 'store']);
            Route::get('/{personalPlan}', [PersonalPlanController::class, 'show']);
            Route::put('/{personalPlan}', [PersonalPlanController::class, 'update']);
            Route::delete('/{personalPlan}', [PersonalPlanController::class, 'cancel']);
            Route::post('/{personalPlan}/rate', [PersonalPlanController::class, 'rate']);
        });
    });
});
