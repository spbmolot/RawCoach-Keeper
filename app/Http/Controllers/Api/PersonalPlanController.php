<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalPlan;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PersonalPlanController extends Controller
{
    /**
     * Список персональных планов пользователя
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $plans = PersonalPlan::where('user_id', $user->id)
            ->with(['nutritionist', 'questionnaire'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    /**
     * Создание нового персонального плана
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Проверяем, есть ли у пользователя подписка на персональные планы
        if (!$user->hasActiveSubscription(['personal'])) {
            return response()->json([
                'success' => false,
                'message' => 'Для создания персонального плана необходима соответствующая подписка'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'goal' => ['required', 'in:weight_loss,weight_gain,maintenance,muscle_gain'],
            'target_weight' => ['nullable', 'numeric', 'min:30', 'max:300'],
            'target_date' => ['nullable', 'date', 'after:today'],
            'dietary_preferences' => ['nullable', 'array'],
            'allergies' => ['nullable', 'array'],
            'health_conditions' => ['nullable', 'array'],
            'activity_level' => ['required', 'in:sedentary,light,moderate,active,very_active'],
            'meal_count' => ['required', 'integer', 'min:3', 'max:6'],
            'budget_range' => ['nullable', 'in:low,medium,high'],
            'cooking_time' => ['nullable', 'in:quick,medium,long'],
            'special_requirements' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        // Создаем анкету
        $questionnaire = Questionnaire::create([
            'user_id' => $user->id,
            'goal' => $request->goal,
            'current_weight' => $user->weight,
            'target_weight' => $request->target_weight,
            'target_date' => $request->target_date,
            'height' => $user->height,
            'age' => $user->birth_date ? now()->diffInYears($user->birth_date) : null,
            'gender' => $user->gender,
            'activity_level' => $request->activity_level,
            'dietary_preferences' => $request->dietary_preferences,
            'allergies' => $request->allergies,
            'health_conditions' => $request->health_conditions,
            'meal_count' => $request->meal_count,
            'budget_range' => $request->budget_range,
            'cooking_time' => $request->cooking_time,
            'special_requirements' => $request->special_requirements,
        ]);

        // Назначаем нутрициолога (простая логика - берем менее загруженного)
        $nutritionist = User::role('nutritionist')
            ->withCount('assignedPlans')
            ->orderBy('assigned_plans_count')
            ->first();

        // Создаем персональный план
        $personalPlan = PersonalPlan::create([
            'user_id' => $user->id,
            'nutritionist_id' => $nutritionist?->id,
            'questionnaire_id' => $questionnaire->id,
            'status' => 'pending',
            'title' => "Персональный план для {$user->name}",
            'description' => 'Индивидуальный план питания на основе ваших целей и предпочтений',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Персональный план создан и передан нутрициологу',
            'data' => $personalPlan->load(['nutritionist', 'questionnaire'])
        ], 201);
    }

    /**
     * Детали персонального плана
     */
    public function show(PersonalPlan $personalPlan): JsonResponse
    {
        $user = auth()->user();

        if ($personalPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        $personalPlan->load(['nutritionist', 'questionnaire', 'days.recipes']);

        return response()->json([
            'success' => true,
            'data' => $personalPlan
        ]);
    }

    /**
     * Обновление персонального плана
     */
    public function update(Request $request, PersonalPlan $personalPlan): JsonResponse
    {
        $user = auth()->user();

        if ($personalPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        if (!in_array($personalPlan->status, ['pending', 'in_progress'])) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя изменить план в текущем статусе'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'special_requirements' => ['nullable', 'string', 'max:1000'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        // Обновляем анкету если есть изменения
        if ($request->has('special_requirements')) {
            $personalPlan->questionnaire->update([
                'special_requirements' => $request->special_requirements
            ]);
        }

        // Добавляем обратную связь
        if ($request->has('feedback')) {
            $personalPlan->update([
                'user_feedback' => $request->feedback
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Персональный план обновлен',
            'data' => $personalPlan->fresh()->load(['nutritionist', 'questionnaire'])
        ]);
    }

    /**
     * Отмена персонального плана
     */
    public function cancel(PersonalPlan $personalPlan): JsonResponse
    {
        $user = auth()->user();

        if ($personalPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        if (!in_array($personalPlan->status, ['pending', 'in_progress', 'active'])) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя отменить план в текущем статусе'
            ], 400);
        }

        $personalPlan->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Персональный план отменен'
        ]);
    }

    /**
     * Оценка персонального плана
     */
    public function rate(Request $request, PersonalPlan $personalPlan): JsonResponse
    {
        $user = auth()->user();

        if ($personalPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав'
            ], 403);
        }

        if ($personalPlan->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Можно оценить только завершенный план'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $personalPlan->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Оценка сохранена',
            'data' => $personalPlan->fresh()
        ]);
    }
}
