@extends('layouts.public')

@section('title', $recipe->title . ' — RawPlan')
@section('description', Str::limit($recipe->description, 160))

@php $activeNav = 'recipes'; @endphp

@push('styles')
<style>
    .nutrition-card { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); }
</style>
@endpush

@push('schema')
<x-schema-recipe :recipe="$recipe" />
@endpush

@section('content')
    <!-- Breadcrumbs -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-green-600">Главная</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <a href="{{ route('recipes.index') }}" class="hover:text-green-600">Рецепты</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-gray-900">{{ $recipe->title }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        <div class="grid lg:grid-cols-3 gap-4 sm:gap-8">
            
            <!-- Left Column: Recipe Details -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-8">
                
                <!-- Recipe Header -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden">
                    @if($recipe->image)
                        <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-full h-48 sm:h-64 md:h-80 object-cover">
                    @else
                        <div class="w-full h-48 sm:h-64 md:h-80 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                            <i data-lucide="chef-hat" class="w-16 h-16 sm:w-24 sm:h-24 text-green-400"></i>
                        </div>
                    @endif
                    
                    <div class="p-4 sm:p-6 md:p-8">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                            <span class="px-2 sm:px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs sm:text-sm font-medium">
                                @switch($recipe->category)
                                    @case('breakfast') Завтрак @break
                                    @case('lunch') Обед @break
                                    @case('dinner') Ужин @break
                                    @case('snack') Перекус @break
                                    @default {{ $recipe->category }}
                                @endswitch
                            </span>
                            <span class="px-2 sm:px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs sm:text-sm font-medium">
                                @switch($recipe->difficulty)
                                    @case('easy') Легко @break
                                    @case('medium') Средне @break
                                    @case('hard') Сложно @break
                                    @default {{ $recipe->difficulty }}
                                @endswitch
                            </span>
                        </div>
                        
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2 sm:mb-3">{{ $recipe->title }}</h1>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-3 mb-3 sm:mb-4" x-data="recipeRating({{ $recipe->id }}, {{ $recipe->rating ?? 0 }}, {{ $recipe->ratings_count ?? 0 }}, {{ $userRating ?? 'null' }})">
                            <div class="flex items-center gap-0.5">
                                <template x-for="star in 5" :key="star">
                                    <button 
                                        @click="rate(star)" 
                                        @mouseenter="hoverRating = star" 
                                        @mouseleave="hoverRating = 0"
                                        class="focus:outline-none transition-transform hover:scale-110"
                                        :class="{'cursor-pointer': {{ auth()->check() ? 'true' : 'false' }}, 'cursor-default': {{ auth()->check() ? 'false' : 'true' }}}"
                                    >
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-colors" 
                                             :class="(hoverRating >= star || (!hoverRating && displayRating >= star)) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <span class="text-sm sm:text-base font-semibold text-gray-700" x-text="avgRating > 0 ? avgRating.toFixed(1) : '—'"></span>
                            <span class="text-xs sm:text-sm text-gray-400" x-text="'(' + ratingsCount + ' ' + pluralize(ratingsCount) + ')'"></span>
                            <span x-show="userRated" x-transition class="text-xs text-green-600 font-medium">Ваша оценка: <span x-text="myRating"></span></span>
                        </div>
                        
                        <p class="text-sm sm:text-lg text-gray-600 mb-4 sm:mb-6">{{ $recipe->description }}</p>
                        
                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-3 sm:gap-6">
                            <div class="flex items-center gap-1.5 sm:gap-2 text-gray-600 text-sm sm:text-base">
                                <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5 text-green-500"></i>
                                <span>{{ $recipe->prep_time + $recipe->cook_time }} мин</span>
                            </div>
                            <div class="flex items-center gap-1.5 sm:gap-2 text-gray-600 text-sm sm:text-base">
                                <i data-lucide="users" class="w-4 h-4 sm:w-5 sm:h-5 text-green-500"></i>
                                <span>{{ $recipe->servings }} {{ $recipe->servings == 1 ? 'порция' : 'порции' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 sm:gap-2 text-gray-600 text-sm sm:text-base">
                                <i data-lucide="flame" class="w-4 h-4 sm:w-5 sm:h-5 text-orange-500"></i>
                                <span>{{ $recipe->calories }} ккал</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients -->
                <x-subscription-gate :locked="!($canAccessFull ?? true)">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 md:p-8">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                        <i data-lucide="shopping-basket" class="w-5 h-5 sm:w-7 sm:h-7 text-green-500"></i>
                        Ингредиенты
                    </h2>
                    
                    <ul class="space-y-2 sm:space-y-3">
                        @foreach($recipe->ingredients as $ingredient)
                            <li class="flex items-center justify-between py-2 sm:py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="check" class="w-3 h-3 sm:w-4 sm:h-4 text-green-500"></i>
                                    </div>
                                    <span class="text-gray-900 text-sm sm:text-base truncate">{{ $ingredient->ingredient_name }}</span>
                                    @if($ingredient->is_optional)
                                        <span class="text-[10px] sm:text-xs text-gray-400 flex-shrink-0">(опц.)</span>
                                    @endif
                                </div>
                                <span class="text-gray-600 font-medium text-xs sm:text-sm flex-shrink-0 ml-2">{{ $ingredient->amount }} {{ $ingredient->unit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                </x-subscription-gate>

                <!-- Instructions -->
                <x-subscription-gate :locked="!($canAccessFull ?? true)">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm overflow-hidden" x-data="cookingSteps({{ count(array_filter(explode("\n", $recipe->instructions), fn($l) => trim($l) && !preg_match('/^(?:\*\*|#{1,3}\s*)?(?:Шаг\s*\d+[:\.]?\s*|Этап\s*\d+[:\.]?\s*)/iu', trim($l)))) }})">
                    <!-- Header with progress -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 sm:p-6 md:p-8">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <h2 class="text-lg sm:text-2xl font-bold text-white flex items-center gap-2 sm:gap-3">
                                <i data-lucide="chef-hat" class="w-5 h-5 sm:w-7 sm:h-7"></i>
                                <span class="hidden xs:inline">Пошаговое</span> Приготовление
                            </h2>
                            <div class="flex items-center gap-1.5 sm:gap-2 text-white/80 text-xs sm:text-sm">
                                <i data-lucide="clock" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                                <span>{{ $recipe->prep_time + $recipe->cook_time }} мин</span>
                            </div>
                        </div>
                        
                        <!-- Progress bar -->
                        <div class="relative">
                            <div class="h-1.5 sm:h-2 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-white rounded-full transition-all duration-500 ease-out" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <div class="flex justify-between mt-1.5 sm:mt-2 text-xs sm:text-sm text-white/80">
                                <span x-text="completedSteps + ' из ' + totalSteps + ' шагов'"></span>
                                <span x-text="progress + '%'" class="font-semibold text-white"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 sm:p-6 md:p-8">
                        @php
                            $lines = array_filter(explode("\n", $recipe->instructions), fn($l) => trim($l));
                            $steps = [];
                            $currentGroup = null;
                            $stepNum = 0;
                            
                            foreach ($lines as $line) {
                                $trimmed = trim($line);
                                if (preg_match('/^(?:\*\*|#{1,3}\s*)?(?:Шаг\s*\d+[:\.]?\s*|Этап\s*\d+[:\.]?\s*)(.+?)(?:\*\*)?$/iu', $trimmed, $m)) {
                                    $currentGroup = trim($m[1], ' *:');
                                } else {
                                    $stepNum++;
                                    $steps[] = [
                                        'num' => $stepNum,
                                        'group' => $currentGroup,
                                        'text' => preg_replace('/^\d+\.\s*/', '', $trimmed)
                                    ];
                                    $currentGroup = null;
                                }
                            }
                            
                            $groupedSteps = [];
                            foreach ($steps as $step) {
                                $groupedSteps[] = array_merge($step, ['displayGroup' => $step['group'] ?: null]);
                            }
                        @endphp
                        
                        <div class="space-y-4">
                            @foreach($groupedSteps as $index => $step)
                                @if($step['displayGroup'])
                                    <!-- Group Header -->
                                    <div class="flex items-center gap-3 py-3">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                            <i data-lucide="bookmark" class="w-4 h-4 text-amber-600"></i>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $step['displayGroup'] }}</span>
                                        <div class="h-px flex-1 bg-gray-200"></div>
                                    </div>
                                @endif
                                
                                <!-- Step Card -->
                                <div 
                                    class="group relative rounded-xl sm:rounded-2xl border-2 transition-all duration-300 cursor-pointer"
                                    :class="steps[{{ $index }}] ? 'bg-green-50 border-green-300' : 'bg-gray-50 border-transparent hover:border-green-200 hover:bg-white'"
                                    @click="toggleStep({{ $index }})"
                                >
                                    <div class="p-3 sm:p-4 md:p-5">
                                        <div class="flex gap-2 sm:gap-4">
                                            <!-- Checkbox / Number -->
                                            <div class="flex-shrink-0">
                                                <div 
                                                    class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl flex items-center justify-center font-bold text-sm sm:text-lg transition-all duration-300"
                                                    :class="steps[{{ $index }}] ? 'bg-green-500 text-white shadow-lg shadow-green-200' : 'bg-white border-2 border-gray-200 text-gray-400 group-hover:border-green-300 group-hover:text-green-500'"
                                                >
                                                    <svg x-show="steps[{{ $index }}]" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                                    <span x-show="!steps[{{ $index }}]">{{ $step['num'] }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div 
                                                    class="leading-relaxed prose prose-sm prose-green max-w-none [&>p]:m-0 [&>ul]:my-2 [&>ol]:my-2 transition-all duration-300"
                                                    :class="steps[{{ $index }}] ? 'text-green-700 [&>p]:line-through [&>p]:opacity-60' : 'text-gray-700'"
                                                >
                                                    {!! Str::markdown($step['text']) !!}
                                                </div>
                                                
                                                <!-- Tip extraction (if contains совет/подсказка) -->
                                                @if(preg_match('/(?:совет|подсказка|💡|tip)[:\s]*(.+)/iu', $step['text'], $tip))
                                                    <div class="mt-3 flex items-start gap-2 p-3 bg-amber-50 rounded-lg border border-amber-100" :class="steps[{{ $index }}] ? 'opacity-50' : ''">
                                                        <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"></i>
                                                        <span class="text-sm text-amber-700">{{ trim($tip[1]) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Status indicator -->
                                            <div class="flex-shrink-0 self-center">
                                                <div 
                                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300"
                                                    :class="steps[{{ $index }}] ? 'bg-green-100' : 'bg-gray-100 opacity-0 group-hover:opacity-100'"
                                                >
                                                    <i data-lucide="check" class="w-4 h-4" :class="steps[{{ $index }}] ? 'text-green-600' : 'text-gray-400'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Completion Card -->
                        <div 
                            class="mt-8 rounded-2xl p-6 text-center transition-all duration-500"
                            :class="progress === 100 ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gray-100 text-gray-400'"
                        >
                            <div class="flex flex-col items-center gap-3">
                                <div 
                                    class="w-16 h-16 rounded-full flex items-center justify-center transition-all duration-500"
                                    :class="progress === 100 ? 'bg-white/20' : 'bg-gray-200'"
                                >
                                    <i data-lucide="award" class="w-8 h-8" :class="progress === 100 ? 'text-white' : 'text-gray-400'"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-lg" x-text="progress === 100 ? '🎉 Блюдо готово!' : 'Завершите все шаги'"></p>
                                    <p class="text-sm mt-1" :class="progress === 100 ? 'text-white/80' : 'text-gray-400'" x-text="progress === 100 ? 'Приятного аппетита!' : 'Нажимайте на шаги, чтобы отмечать выполненные'"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reset button -->
                        <div class="mt-4 text-center" x-show="completedSteps > 0" x-transition>
                            <button 
                                @click.stop="resetSteps()" 
                                class="text-sm text-gray-500 hover:text-gray-700 underline underline-offset-2"
                            >
                                Сбросить прогресс
                            </button>
                        </div>
                    </div>
                </div>
                </x-subscription-gate>
            </div>

            <!-- Right Column: Nutrition & Actions -->
            <div class="space-y-4 sm:space-y-6">
                
                <!-- Nutrition Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 sticky top-20 sm:top-24">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-4 flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-5 h-5 sm:w-6 sm:h-6 text-green-500"></i>
                        Пищевая ценность
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">На 1 порцию</p>
                    
                    <div class="space-y-3 sm:space-y-4">
                        <!-- Calories -->
                        <div class="nutrition-card rounded-lg sm:rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <i data-lucide="flame" class="w-5 h-5 sm:w-6 sm:h-6 text-orange-500"></i>
                                    <span class="font-medium text-gray-900 text-sm sm:text-base">Калории</span>
                                </div>
                                <span class="text-xl sm:text-2xl font-bold text-gray-900">{{ $recipe->calories }}</span>
                            </div>
                        </div>
                        
                        <!-- Macros -->
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <div class="bg-blue-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                                <div class="text-lg sm:text-2xl font-bold text-blue-600">{{ $recipe->proteins }}</div>
                                <div class="text-[10px] sm:text-sm text-gray-600">Белки (г)</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                                <div class="text-lg sm:text-2xl font-bold text-yellow-600">{{ $recipe->fats }}</div>
                                <div class="text-[10px] sm:text-sm text-gray-600">Жиры (г)</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                                <div class="text-lg sm:text-2xl font-bold text-purple-600">{{ $recipe->carbs }}</div>
                                <div class="text-[10px] sm:text-sm text-gray-600">Углеводы (г)</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-4 sm:mt-6 space-y-2 sm:space-y-3">
                        @auth
                            <button onclick="toggleFavorite({{ $recipe->id }})" id="favoriteBtn" class="w-full py-2.5 sm:py-3 px-3 sm:px-4 rounded-lg sm:rounded-xl font-semibold transition flex items-center justify-center gap-2 text-sm sm:text-base {{ $isFavorite ?? false ? 'bg-red-50 text-red-600 border-2 border-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <i data-lucide="heart" class="w-4 h-4 sm:w-5 sm:h-5 {{ $isFavorite ?? false ? 'fill-red-500' : '' }}"></i>
                                <span>{{ $isFavorite ?? false ? 'В избранном' : 'В избранное' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-full py-2.5 sm:py-3 px-3 sm:px-4 bg-gray-100 text-gray-700 rounded-lg sm:rounded-xl font-semibold hover:bg-gray-200 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                                <i data-lucide="heart" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                В избранное
                            </a>
                        @endauth
                        
                        <button onclick="window.print()" class="w-full py-2.5 sm:py-3 px-3 sm:px-4 bg-gray-100 text-gray-700 rounded-lg sm:rounded-xl font-semibold hover:bg-gray-200 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i data-lucide="printer" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            Распечатать
                        </button>
                    </div>
                    
                    <!-- Dietary Tags -->
                    @if($recipe->dietary_tags && count($recipe->dietary_tags) > 0)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-500 mb-3">Особенности</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($recipe->dietary_tags as $tag)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                                        @switch($tag)
                                            @case('vegetarian') Вегетарианское @break
                                            @case('vegan') Веганское @break
                                            @case('gluten-free') Без глютена @break
                                            @case('high-protein') Высокобелковое @break
                                            @case('low-carb') Низкоуглеводное @break
                                            @case('low-fat') Низкожировое @break
                                            @case('high-fiber') Богато клетчаткой @break
                                            @case('omega-3') Омега-3 @break
                                            @case('low-calorie') Низкокалорийное @break
                                            @default {{ $tag }}
                                        @endswitch
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Similar Recipes -->
        @if(isset($similarRecipes) && $similarRecipes->count() > 0)
            <section class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Похожие рецепты</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($similarRecipes as $similar)
                        <a href="{{ route('recipes.show', $similar) }}" class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">
                            @if($similar->image)
                                <img src="{{ Storage::url($similar->image) }}" alt="{{ $similar->title }}" class="w-full h-40 object-cover group-hover:scale-105 transition">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                                    <i data-lucide="chef-hat" class="w-12 h-12 text-green-400"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 group-hover:text-green-600 transition">{{ $similar->title }}</h3>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>{{ $similar->calories }} ккал</span>
                                    <span>{{ $similar->prep_time + $similar->cook_time }} мин</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

@endsection

@push('scripts')
<script>
    function recipeRating(recipeId, initialRating, initialCount, initialUserRating) {
        return {
            recipeId: recipeId,
            avgRating: initialRating,
            ratingsCount: initialCount,
            myRating: initialUserRating,
            hoverRating: 0,
            userRated: initialUserRating !== null,
            isSubmitting: false,

            get displayRating() {
                return this.myRating || Math.round(this.avgRating);
            },

            pluralize(n) {
                if (n % 10 === 1 && n % 100 !== 11) return 'оценка';
                if (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20)) return 'оценки';
                return 'оценок';
            },

            rate(star) {
                @auth
                if (this.isSubmitting) return;
                this.isSubmitting = true;
                this.myRating = star;
                this.userRated = true;

                fetch(`/recipes/${this.recipeId}/rate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ rating: star })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.avgRating = data.rating;
                        this.ratingsCount = data.ratings_count;
                    }
                })
                .finally(() => { this.isSubmitting = false; });
                @else
                window.location.href = '{{ route("login") }}';
                @endauth
            }
        };
    }

    function toggleFavorite(recipeId) {
        fetch(`/recipes/${recipeId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
    
    function cookingSteps(totalSteps) {
        return {
            totalSteps: totalSteps,
            steps: Array(totalSteps).fill(false),
            
            init() {
                // Восстанавливаем прогресс из localStorage
                const saved = localStorage.getItem('recipe_{{ $recipe->id }}_steps');
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        if (Array.isArray(parsed) && parsed.length === this.totalSteps) {
                            this.steps = parsed;
                        }
                    } catch (e) {}
                }
            },
            
            get completedSteps() {
                return this.steps.filter(Boolean).length;
            },
            
            get progress() {
                return this.totalSteps > 0 ? Math.round((this.completedSteps / this.totalSteps) * 100) : 0;
            },
            
            toggleStep(index) {
                this.steps[index] = !this.steps[index];
                this.saveProgress();
            },
            
            resetSteps() {
                this.steps = Array(this.totalSteps).fill(false);
                this.saveProgress();
            },
            
            saveProgress() {
                localStorage.setItem('recipe_{{ $recipe->id }}_steps', JSON.stringify(this.steps));
            }
        };
    }
</script>
@endpush
