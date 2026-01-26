<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $recipe->title }} | RawPlan</title>
    <meta name="description" content="{{ Str::limit($recipe->description, 160) }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #065f46 0%, #047857 50%, #10b981 100%); }
        .nutrition-card { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); }
    </style>
</head>
<body class="antialiased text-gray-900 bg-gray-50">

    <!-- Header -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                        <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">RawPlan</span>
                </a>
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('recipes.index') }}" class="text-green-600 font-medium">Рецепты</a>
                    <a href="{{ route('menus.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Меню</a>
                    <a href="{{ route('home') }}#pricing" class="text-gray-600 hover:text-green-600 font-medium transition">Тарифы</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                            Мой кабинет
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 font-medium hover:text-green-600 transition">Войти</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25">
                            Начать бесплатно
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Recipe Details -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Recipe Header -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @if($recipe->image)
                        <img src="{{ Storage::url($recipe->image) }}" alt="{{ $recipe->title }}" class="w-full h-64 md:h-80 object-cover">
                    @else
                        <div class="w-full h-64 md:h-80 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                            <i data-lucide="chef-hat" class="w-24 h-24 text-green-400"></i>
                        </div>
                    @endif
                    
                    <div class="p-6 md:p-8">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                @switch($recipe->category)
                                    @case('breakfast') Завтрак @break
                                    @case('lunch') Обед @break
                                    @case('dinner') Ужин @break
                                    @case('snack') Перекус @break
                                    @default {{ $recipe->category }}
                                @endswitch
                            </span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                                @switch($recipe->difficulty)
                                    @case('easy') Легко @break
                                    @case('medium') Средне @break
                                    @case('hard') Сложно @break
                                    @default {{ $recipe->difficulty }}
                                @endswitch
                            </span>
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $recipe->title }}</h1>
                        
                        <p class="text-lg text-gray-600 mb-6">{{ $recipe->description }}</p>
                        
                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-6">
                            <div class="flex items-center gap-2 text-gray-600">
                                <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
                                <span>{{ $recipe->prep_time + $recipe->cook_time }} мин</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i data-lucide="users" class="w-5 h-5 text-green-500"></i>
                                <span>{{ $recipe->servings }} {{ $recipe->servings == 1 ? 'порция' : 'порции' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i data-lucide="flame" class="w-5 h-5 text-orange-500"></i>
                                <span>{{ $recipe->calories }} ккал</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients -->
                <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <i data-lucide="shopping-basket" class="w-7 h-7 text-green-500"></i>
                        Ингредиенты
                    </h2>
                    
                    <ul class="space-y-3">
                        @foreach($recipe->ingredients as $ingredient)
                            <li class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                                    </div>
                                    <span class="text-gray-900">{{ $ingredient->ingredient_name }}</span>
                                    @if($ingredient->is_optional)
                                        <span class="text-xs text-gray-400">(по желанию)</span>
                                    @endif
                                </div>
                                <span class="text-gray-600 font-medium">{{ $ingredient->amount }} {{ $ingredient->unit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Instructions -->
                <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <i data-lucide="list-ordered" class="w-7 h-7 text-green-500"></i>
                        Приготовление
                    </h2>
                    
                    <div class="space-y-4">
                        @foreach(explode("\n", $recipe->instructions) as $index => $step)
                            @if(trim($step))
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-gray-700 pt-1">{{ preg_replace('/^\d+\.\s*/', '', trim($step)) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Nutrition & Actions -->
            <div class="space-y-6">
                
                <!-- Nutrition Card -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-6 h-6 text-green-500"></i>
                        Пищевая ценность
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">На 1 порцию</p>
                    
                    <div class="space-y-4">
                        <!-- Calories -->
                        <div class="nutrition-card rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="flame" class="w-6 h-6 text-orange-500"></i>
                                    <span class="font-medium text-gray-900">Калории</span>
                                </div>
                                <span class="text-2xl font-bold text-gray-900">{{ $recipe->calories }}</span>
                            </div>
                        </div>
                        
                        <!-- Macros -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-blue-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $recipe->proteins }}</div>
                                <div class="text-sm text-gray-600">Белки (г)</div>
                            </div>
                            <div class="bg-yellow-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $recipe->fats }}</div>
                                <div class="text-sm text-gray-600">Жиры (г)</div>
                            </div>
                            <div class="bg-purple-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $recipe->carbs }}</div>
                                <div class="text-sm text-gray-600">Углеводы (г)</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-6 space-y-3">
                        @auth
                            <button onclick="toggleFavorite({{ $recipe->id }})" id="favoriteBtn" class="w-full py-3 px-4 rounded-xl font-semibold transition flex items-center justify-center gap-2 {{ $isFavorite ?? false ? 'bg-red-50 text-red-600 border-2 border-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <i data-lucide="heart" class="w-5 h-5 {{ $isFavorite ?? false ? 'fill-red-500' : '' }}"></i>
                                <span>{{ $isFavorite ?? false ? 'В избранном' : 'В избранное' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 px-4 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition flex items-center justify-center gap-2">
                                <i data-lucide="heart" class="w-5 h-5"></i>
                                В избранное
                            </a>
                        @endauth
                        
                        <button onclick="window.print()" class="w-full py-3 px-4 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-5 h-5"></i>
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

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm">© {{ date('Y') }} RawPlan. Все права защищены.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
        
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
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
