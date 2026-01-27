<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalPlan;
use App\Models\Questionnaire;
use App\Models\User;
use Carbon\Carbon;

class PersonalPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Список персональных планов пользователя
     */
    public function index()
    {
        $user = auth()->user();
        
        $personalPlans = PersonalPlan::where('user_id', $user->id)
            ->with(['questionnaire', 'nutritionist'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('personal-plans.index', compact('personalPlans'));
    }

    /**
     * Показать конкретный персональный план
     */
    public function show(PersonalPlan $personalPlan)
    {
        $this->authorize('view', $personalPlan);

        $personalPlan->load([
            'questionnaire',
            'nutritionist',
            'menus.days.recipes',
            'recommendations'
        ]);

        return view('personal-plans.show', compact('personalPlan'));
    }

    /**
     * Форма создания нового персонального плана
     */
    public function create()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->with('plan')->first();

        // Проверяем, есть ли персональная подписка
        if (!$subscription || $subscription->plan->slug !== 'personal') {
            return redirect()->route('plans.index')
                ->with('error', 'Для создания персонального плана необходима персональная подписка');
        }

        // Проверяем лимит персональных планов
        $existingPlansCount = PersonalPlan::where('user_id', $user->id)->count();
        $planLimit = $subscription->plan->features['personal_plans_limit'] ?? 1;

        if ($existingPlansCount >= $planLimit) {
            return redirect()->route('personal-plans.index')
                ->with('error', "Достигнут лимит персональных планов ({$planLimit})");
        }

        return view('personal-plans.create');
    }

    /**
     * Сохранение анкеты и создание персонального плана
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->with('plan')->first();

        if (!$subscription || $subscription->plan->slug !== 'personal') {
            return redirect()->route('plans.index')
                ->with('error', 'Для создания персонального плана необходима персональная подписка');
        }

        $request->validate([
            'goal' => 'required|in:weight_loss,weight_gain,maintenance,muscle_gain',
            'current_weight' => 'required|numeric|min:30|max:300',
            'target_weight' => 'required|numeric|min:30|max:300',
            'height' => 'required|integer|min:100|max:250',
            'age' => 'required|integer|min:16|max:100',
            'gender' => 'required|in:male,female',
            'activity_level' => 'required|in:sedentary,light,moderate,active,very_active',
            'dietary_preferences' => 'nullable|array',
            'allergies' => 'nullable|array',
            'medical_conditions' => 'nullable|array',
            'meal_preferences' => 'nullable|array',
            'cooking_time_preference' => 'required|in:quick,medium,any',
            'budget_level' => 'required|in:low,medium,high',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            // Создаем анкету
            $questionnaire = Questionnaire::create([
                'user_id' => $user->id,
                'goal' => $request->goal,
                'current_weight' => $request->current_weight,
                'target_weight' => $request->target_weight,
                'height' => $request->height,
                'age' => $request->age,
                'gender' => $request->gender,
                'activity_level' => $request->activity_level,
                'dietary_preferences' => $request->dietary_preferences ?? [],
                'allergies' => $request->allergies ?? [],
                'medical_conditions' => $request->medical_conditions ?? [],
                'meal_preferences' => $request->meal_preferences ?? [],
                'cooking_time_preference' => $request->cooking_time_preference,
                'budget_level' => $request->budget_level,
                'additional_notes' => $request->additional_notes,
            ]);

            // Создаем персональный план
            $personalPlan = PersonalPlan::create([
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaire->id,
                'status' => 'pending',
                'requested_at' => Carbon::now(),
            ]);

            \DB::commit();

            return redirect()->route('personal-plans.show', $personalPlan)
                ->with('success', 'Анкета отправлена! Наш нутрициолог создаст для вас персональный план в течение 3-5 рабочих дней.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Произошла ошибка при создании персонального плана');
        }
    }

    /**
     * Форма редактирования анкеты
     */
    public function edit(PersonalPlan $personalPlan)
    {
        $this->authorize('update', $personalPlan);

        if ($personalPlan->status !== 'pending') {
            return redirect()->route('personal-plans.show', $personalPlan)
                ->with('error', 'Нельзя редактировать анкету после начала работы нутрициолога');
        }

        $personalPlan->load('questionnaire');

        return view('personal-plans.edit', compact('personalPlan'));
    }

    /**
     * Обновление анкеты
     */
    public function update(Request $request, PersonalPlan $personalPlan)
    {
        $this->authorize('update', $personalPlan);

        if ($personalPlan->status !== 'pending') {
            return redirect()->route('personal-plans.show', $personalPlan)
                ->with('error', 'Нельзя редактировать анкету после начала работы нутрициолога');
        }

        $request->validate([
            'goal' => 'required|in:weight_loss,weight_gain,maintenance,muscle_gain',
            'current_weight' => 'required|numeric|min:30|max:300',
            'target_weight' => 'required|numeric|min:30|max:300',
            'height' => 'required|integer|min:100|max:250',
            'age' => 'required|integer|min:16|max:100',
            'gender' => 'required|in:male,female',
            'activity_level' => 'required|in:sedentary,light,moderate,active,very_active',
            'dietary_preferences' => 'nullable|array',
            'allergies' => 'nullable|array',
            'medical_conditions' => 'nullable|array',
            'meal_preferences' => 'nullable|array',
            'cooking_time_preference' => 'required|in:quick,medium,any',
            'budget_level' => 'required|in:low,medium,high',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        $personalPlan->questionnaire->update([
            'goal' => $request->goal,
            'current_weight' => $request->current_weight,
            'target_weight' => $request->target_weight,
            'height' => $request->height,
            'age' => $request->age,
            'gender' => $request->gender,
            'activity_level' => $request->activity_level,
            'dietary_preferences' => $request->dietary_preferences ?? [],
            'allergies' => $request->allergies ?? [],
            'medical_conditions' => $request->medical_conditions ?? [],
            'meal_preferences' => $request->meal_preferences ?? [],
            'cooking_time_preference' => $request->cooking_time_preference,
            'budget_level' => $request->budget_level,
            'additional_notes' => $request->additional_notes,
        ]);

        return redirect()->route('personal-plans.show', $personalPlan)
            ->with('success', 'Анкета успешно обновлена');
    }

    /**
     * Отмена персонального плана
     */
    public function cancel(PersonalPlan $personalPlan)
    {
        $this->authorize('delete', $personalPlan);

        if (!in_array($personalPlan->status, ['pending', 'in_progress'])) {
            return redirect()->route('personal-plans.show', $personalPlan)
                ->with('error', 'Нельзя отменить завершенный план');
        }

        $personalPlan->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);

        return redirect()->route('personal-plans.index')
            ->with('success', 'Персональный план отменен');
    }

    /**
     * Оценка персонального плана
     */
    public function rate(Request $request, PersonalPlan $personalPlan)
    {
        $this->authorize('view', $personalPlan);

        if ($personalPlan->status !== 'completed') {
            return response()->json(['error' => 'Можно оценить только завершенный план'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $personalPlan->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
            'rated_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Спасибо за вашу оценку!'
        ]);
    }

    /**
     * Скачать персональный план
     */
    public function download(PersonalPlan $personalPlan, Request $request)
    {
        $this->authorize('view', $personalPlan);

        if ($personalPlan->status !== 'completed') {
            return redirect()->route('personal-plans.show', $personalPlan)
                ->with('error', 'Можно скачать только завершенный план');
        }

        $format = $request->get('format', 'pdf');
        $filename = "personal-plan-{$personalPlan->id}.{$format}";

        $personalPlan->load([
            'questionnaire',
            'nutritionist',
            'menus.days.recipes',
            'recommendations'
        ]);

        if ($format === 'pdf') {
            // return PDF::loadView('exports.personal-plan-pdf', compact('personalPlan'))->download($filename);
        } else {
            // return Excel::download(new PersonalPlanExport($personalPlan), $filename);
        }

        return back()->with('success', 'Персональный план скачан');
    }

    /**
     * Чат с нутрициологом (если план в работе)
     */
    public function chat(PersonalPlan $personalPlan)
    {
        $this->authorize('view', $personalPlan);

        if (!in_array($personalPlan->status, ['in_progress', 'completed'])) {
            return redirect()->route('personal-plans.show', $personalPlan)
                ->with('error', 'Чат доступен только после начала работы нутрициолога');
        }

        $messages = $personalPlan->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('personal-plans.chat', compact('personalPlan', 'messages'));
    }

    /**
     * Отправка сообщения нутрициологу
     */
    public function sendMessage(Request $request, PersonalPlan $personalPlan)
    {
        $this->authorize('view', $personalPlan);

        if (!in_array($personalPlan->status, ['in_progress', 'completed'])) {
            return response()->json(['error' => 'Чат недоступен'], 400);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $personalPlan->messages()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'sent_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }
}
