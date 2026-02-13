<?php

namespace App\Http\Controllers;

use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Models\AdCreative;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdCampaignController extends Controller
{
    /**
     * Конструктор - проверяем права доступа
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:advertiser|admin']);
    }

    /**
     * Список рекламных кампаний
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $query = AdCampaign::with(['placements', 'creatives'])
            ->when(!$user->hasRole('admin'), function ($q) use ($user) {
                return $q->where('advertiser_id', $user->id);
            })
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Фильтрация по дате
        if ($request->has('date_from')) {
            $query->where('starts_at', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('ends_at', '<=', $request->get('date_to'));
        }

        $campaigns = $query->paginate(15);

        return view('ad-campaigns.index', compact('campaigns'));
    }

    /**
     * Форма создания кампании
     */
    public function create(): View
    {
        $placements = AdPlacement::where('is_active', true)->get();
        
        return view('ad-campaigns.create', compact('placements'));
    }

    /**
     * Сохранение новой кампании
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'budget' => ['required', 'numeric', 'min:0'],
            'daily_budget' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['required', 'date', 'after_or_equal:today'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'target_audience' => ['nullable', 'json'],
            'placement_ids' => ['required', 'array'],
            'placement_ids.*' => ['exists:ad_placements,id'],
        ]);

        $campaign = AdCampaign::create([
            'advertiser_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'daily_budget' => $validated['daily_budget'],
            'spent_budget' => 0,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'target_audience' => $validated['target_audience'],
            'status' => 'pending',
        ]);

        // Привязываем размещения
        $campaign->placements()->attach($validated['placement_ids']);

        return redirect()->route('ad-campaigns.show', $campaign)
            ->with('success', 'Рекламная кампания создана и отправлена на модерацию');
    }

    /**
     * Детали кампании
     */
    public function show(AdCampaign $adCampaign): View
    {
        $this->authorize('view', $adCampaign);

        $adCampaign->load(['placements', 'creatives', 'advertiser']);

        // Статистика кампании
        $stats = [
            'impressions' => $adCampaign->impressions ?? 0,
            'clicks' => $adCampaign->clicks ?? 0,
            'ctr' => $adCampaign->impressions > 0 ? round(($adCampaign->clicks / $adCampaign->impressions) * 100, 2) : 0,
            'spent' => $adCampaign->spent_budget,
            'remaining' => $adCampaign->budget - $adCampaign->spent_budget,
            'days_left' => Carbon::parse($adCampaign->ends_at)->diffInDays(now(), false),
        ];

        return view('ad-campaigns.show', compact('adCampaign', 'stats'));
    }

    /**
     * Форма редактирования кампании
     */
    public function edit(AdCampaign $adCampaign): View
    {
        $this->authorize('update', $adCampaign);

        $placements = AdPlacement::where('is_active', true)->get();
        
        return view('ad-campaigns.edit', compact('adCampaign', 'placements'));
    }

    /**
     * Обновление кампании
     */
    public function update(Request $request, AdCampaign $adCampaign): RedirectResponse
    {
        $this->authorize('update', $adCampaign);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'budget' => ['required', 'numeric', 'min:0'],
            'daily_budget' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['required', 'date', 'after_or_equal:today'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'target_audience' => ['nullable', 'json'],
            'placement_ids' => ['required', 'array'],
            'placement_ids.*' => ['exists:ad_placements,id'],
        ]);

        $adCampaign->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'daily_budget' => $validated['daily_budget'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'target_audience' => $validated['target_audience'],
            'status' => 'pending', // Возвращаем на модерацию
        ]);

        // Обновляем размещения
        $adCampaign->placements()->sync($validated['placement_ids']);

        return redirect()->route('ad-campaigns.show', $adCampaign)
            ->with('success', 'Кампания обновлена и отправлена на модерацию');
    }

    /**
     * Удаление кампании
     */
    public function destroy(AdCampaign $adCampaign): RedirectResponse
    {
        $this->authorize('delete', $adCampaign);

        $adCampaign->delete();

        return redirect()->route('ad-campaigns.index')
            ->with('success', 'Кампания удалена');
    }

    /**
     * Приостановка кампании
     */
    public function pause(AdCampaign $adCampaign): RedirectResponse
    {
        $this->authorize('pause', $adCampaign);

        $adCampaign->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        return redirect()->route('ad-campaigns.show', $adCampaign)
            ->with('success', 'Кампания приостановлена');
    }

    /**
     * Возобновление кампании
     */
    public function resume(AdCampaign $adCampaign): RedirectResponse
    {
        $this->authorize('resume', $adCampaign);

        $adCampaign->update([
            'status' => 'active',
            'paused_at' => null,
        ]);

        return redirect()->route('ad-campaigns.show', $adCampaign)
            ->with('success', 'Кампания возобновлена');
    }

    /**
     * Статистика кампании
     */
    public function stats(AdCampaign $adCampaign): View
    {
        $this->authorize('stats', $adCampaign);

        // Получаем реальную статистику по дням за последние 30 дней
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $statsFromDb = $adCampaign->dailyStats()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(fn($stat) => $stat->date->format('Y-m-d'));

        // Заполняем все 30 дней (нулями если данных нет)
        $dailyStats = collect(range(0, 29))->map(function ($day) use ($statsFromDb) {
            $date = Carbon::now()->subDays(29 - $day);
            $dateKey = $date->format('Y-m-d');
            $stat = $statsFromDb->get($dateKey);

            return [
                'date' => $dateKey,
                'impressions' => $stat->impressions ?? 0,
                'clicks' => $stat->clicks ?? 0,
                'spent' => $stat->spent ?? 0,
            ];
        });

        return view('ad-campaigns.stats', compact('adCampaign', 'dailyStats'));
    }

    /**
     * Управление креативами кампании
     */
    public function creatives(AdCampaign $adCampaign): View
    {
        $this->authorize('manageCreatives', $adCampaign);

        $creatives = $adCampaign->creatives()->orderBy('created_at', 'desc')->get();

        return view('ad-campaigns.creatives', compact('adCampaign', 'creatives'));
    }

    /**
     * Добавление креатива
     */
    public function storeCreative(Request $request, AdCampaign $adCampaign): RedirectResponse
    {
        $this->authorize('manageCreatives', $adCampaign);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:banner,video,text'],
            'content' => ['required_if:type,text', 'nullable', 'string'],
            'image' => ['required_if:type,banner', 'nullable', 'image', 'max:2048'],
            'video' => ['required_if:type,video', 'nullable', 'file', 'mimes:mp4,avi,mov', 'max:10240'],
            'url' => ['required', 'url'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $creativePath = null;
        if ($request->hasFile('image')) {
            $creativePath = $request->file('image')->store('creatives', 'public');
        } elseif ($request->hasFile('video')) {
            $creativePath = $request->file('video')->store('creatives', 'public');
        }

        AdCreative::create([
            'campaign_id' => $adCampaign->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'content' => $validated['content'],
            'file_path' => $creativePath,
            'url' => $validated['url'],
            'alt_text' => $validated['alt_text'],
            'status' => 'pending',
        ]);

        return redirect()->route('ad-campaigns.creatives', $adCampaign)
            ->with('success', 'Креатив добавлен и отправлен на модерацию');
    }
}
