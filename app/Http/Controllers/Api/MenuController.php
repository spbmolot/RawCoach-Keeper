<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class MenuController extends Controller
{
    /**
     * Список меню
     */
    public function index(Request $request): JsonResponse
    {
        $query = Menu::with(['days.recipes'])
            ->where('status', 'published')
            ->orderBy('period_start', 'desc');

        // Фильтрация по дате
        if ($request->has('year') && $request->has('month')) {
            $year = $request->get('year');
            $month = $request->get('month');
            $query->whereYear('period_start', $year)
                  ->whereMonth('period_start', $month);
        }

        // Пагинация
        $menus = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * Детали меню
     */
    public function show(Menu $menu): JsonResponse
    {
        // Проверяем доступ к меню
        $user = auth()->user();
        if ($user && !$this->hasMenuAccess($menu, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав для просмотра этого меню'
            ], 403);
        }

        $menu->load(['days.recipes', 'days.recipes.ingredients']);

        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Архивные меню (только для подписчиков)
     */
    public function archive(Request $request): JsonResponse
    {
        $this->middleware('subscription:archive');

        $menus = Menu::with(['days.recipes'])
            ->where('status', 'published')
            ->where('period_start', '<', now()->startOfMonth())
            ->orderBy('period_start', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * Ранний доступ к меню
     */
    public function early(Request $request): JsonResponse
    {
        $this->middleware('subscription:early');

        $menus = Menu::with(['days.recipes'])
            ->where('status', 'published')
            ->where('period_start', '>', now()->endOfMonth())
            ->orderBy('period_start', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * Избранные меню
     */
    public function favorites(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $favoriteMenuIds = UserFavorite::where('user_id', $user->id)
            ->where('favorable_type', Menu::class)
            ->pluck('favorable_id');

        $menus = Menu::with(['days.recipes'])
            ->whereIn('id', $favoriteMenuIds)
            ->orderBy('period_start', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * Добавить/убрать из избранного
     */
    public function favorite(Request $request, Menu $menu): JsonResponse
    {
        $user = auth()->user();

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('favorable_type', Menu::class)
            ->where('favorable_id', $menu->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Меню удалено из избранного';
            $isFavorite = false;
        } else {
            UserFavorite::create([
                'user_id' => $user->id,
                'favorable_type' => Menu::class,
                'favorable_id' => $menu->id,
            ]);
            $message = 'Меню добавлено в избранное';
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'is_favorite' => $isFavorite
            ]
        ]);
    }

    /**
     * Проверка доступа к меню
     */
    private function hasMenuAccess(Menu $menu, $user): bool
    {
        $now = Carbon::now();
        $menuStart = Carbon::parse($menu->period_start);
        $menuEnd = Carbon::parse($menu->period_end);

        // Текущее меню доступно всем авторизованным
        if ($menuStart->lte($now) && $menuEnd->gte($now)) {
            return true;
        }

        // Архивное меню - нужна подписка с архивом
        if ($menuEnd->lt($now)) {
            return $user->hasActiveSubscription(['premium', 'personal']);
        }

        // Будущее меню - нужна подписка с ранним доступом
        if ($menuStart->gt($now)) {
            return $user->hasActiveSubscription(['premium', 'personal']);
        }

        return false;
    }
}
