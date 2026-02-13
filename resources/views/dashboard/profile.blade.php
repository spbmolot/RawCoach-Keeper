<x-app-layout>
    <div class="max-w-6xl mx-auto" x-data="{ activeTab: 'personal' }">
        {{-- Hero секция с аватаром --}}
        <div class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 rounded-3xl p-8 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
            <div class="relative flex flex-col md:flex-row items-center gap-6">
                {{-- Аватар --}}
                <div class="relative">
                    <div class="w-28 h-28 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-4xl font-bold shadow-xl">
                        @if($user->profile_photo_url)
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-full h-full rounded-2xl object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    @if($user->activeSubscription)
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center shadow-lg">
                            <i data-lucide="crown" class="w-4 h-4 text-yellow-900"></i>
                        </div>
                    @endif
                </div>
                
                {{-- Информация --}}
                <div class="text-center md:text-left flex-1">
                    <h1 class="text-3xl font-bold text-white mb-1">{{ $user->name }}</h1>
                    <p class="text-green-100 mb-3">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        @if($user->activeSubscription)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm text-white">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                {{ $user->activeSubscription->plan->name }}
                            </span>
                        @endif
                        @php
                            $totalHours = $user->created_at->diffInHours(now());
                            $days = floor($totalHours / 24);
                            $hours = $totalHours % 24;
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm text-white">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            С нами {{ $days }} {{ trans_choice('день|дня|дней', $days) }} {{ $hours }} {{ trans_choice('час|часа|часов', $hours) }}
                        </span>
                    </div>
                </div>

                {{-- Прогресс профиля --}}
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 min-w-[200px]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-green-100">Профиль заполнен</span>
                        <span class="text-lg font-bold text-white">{{ $profileProgress }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2.5">
                        <div class="bg-white h-2.5 rounded-full transition-all duration-500" style="width: {{ $profileProgress }}%"></div>
                    </div>
                    @if($profileProgress < 100)
                        <p class="text-xs text-green-100 mt-2">Заполните профиль для персональных рекомендаций</p>
                    @else
                        <p class="text-xs text-green-100 mt-2 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                            Профиль полностью заполнен
                        </p>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3 animate-fade-in">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Левая колонка - Статистика и ИМТ --}}
            <div class="space-y-6">
                {{-- Карточка ИМТ --}}
                @if($user->height && $user->weight)
                    @php
                        $bmi = $user->getBMI();
                        $bmiStatus = $user->getBMIStatus();
                        $bmiColor = match(true) {
                            $bmi < 18.5 => 'blue',
                            $bmi < 25 => 'green',
                            $bmi < 30 => 'yellow',
                            default => 'red'
                        };
                        $bmiPercent = min(100, max(0, (($bmi - 15) / 25) * 100));
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i data-lucide="scale" class="w-5 h-5 text-{{ $bmiColor }}-500"></i>
                            Индекс массы тела
                        </h3>
                        <div class="text-center mb-4">
                            <div class="text-4xl font-bold text-{{ $bmiColor }}-500">{{ $bmi }}</div>
                            <div class="text-sm text-gray-500 mt-1">{{ $bmiStatus }}</div>
                        </div>
                        <div class="relative h-3 bg-gradient-to-r from-blue-400 via-green-400 via-yellow-400 to-red-400 rounded-full mb-2">
                            <div class="absolute w-4 h-4 bg-white border-2 border-gray-800 rounded-full -top-0.5 shadow-md transition-all" style="left: calc({{ $bmiPercent }}% - 8px)"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>15</span>
                            <span>18.5</span>
                            <span>25</span>
                            <span>30</span>
                            <span>40</span>
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="scale" class="w-5 h-5 text-white"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900">Рассчитайте ИМТ</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Укажите рост и вес для расчета индекса массы тела</p>
                        <button @click="activeTab = 'body'" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Заполнить данные →
                        </button>
                    </div>
                @endif

                {{-- Статистика --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-indigo-500"></i>
                        Статистика
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="heart" class="w-5 h-5 text-red-500"></i>
                                </div>
                                <span class="text-gray-600">Избранных рецептов</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ $stats['favorite_recipes'] }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="credit-card" class="w-5 h-5 text-green-600"></i>
                                </div>
                                <span class="text-gray-600">Платежей</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ $stats['payments_count'] }}</span>
                        </div>
                        @if($stats['total_spent'] > 0)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i data-lucide="wallet" class="w-5 h-5 text-purple-600"></i>
                                    </div>
                                    <span class="text-gray-600">Всего оплачено</span>
                                </div>
                                <span class="text-xl font-bold text-gray-900">{{ number_format($stats['total_spent'], 0, ',', ' ') }} ₽</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Цель по весу --}}
                @if($user->weight && $user->target_weight)
                    @php
                        $weightDiff = $user->weight - $user->target_weight;
                        $isLosing = $weightDiff > 0;
                    @endphp
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-100">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i data-lucide="target" class="w-5 h-5 text-amber-500"></i>
                            Цель по весу
                        </h3>
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $user->weight }}</div>
                                <div class="text-xs text-gray-500">Текущий</div>
                            </div>
                            <div class="flex-1 px-4">
                                <div class="flex items-center justify-center">
                                    <i data-lucide="{{ $isLosing ? 'trending-down' : 'trending-up' }}" class="w-6 h-6 text-amber-500"></i>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-amber-600">{{ $user->target_weight }}</div>
                                <div class="text-xs text-gray-500">Цель</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 text-center">
                            {{ $isLosing ? 'Осталось сбросить' : 'Осталось набрать' }}: <strong>{{ abs($weightDiff) }} кг</strong>
                        </div>
                    </div>
                @endif

                {{-- Быстрые ссылки --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="link" class="w-5 h-5 text-gray-500"></i>
                        Быстрые ссылки
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition text-gray-700 hover:text-gray-900">
                            <i data-lucide="shield" class="w-5 h-5 text-gray-400"></i>
                            <span>Безопасность аккаунта</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto"></i>
                        </a>
                        <a href="{{ route('payment.history') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition text-gray-700 hover:text-gray-900">
                            <i data-lucide="receipt" class="w-5 h-5 text-gray-400"></i>
                            <span>История платежей</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto"></i>
                        </a>
                        <a href="{{ route('plans.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition text-gray-700 hover:text-gray-900">
                            <i data-lucide="crown" class="w-5 h-5 text-gray-400"></i>
                            <span>Управление подпиской</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Правая колонка - Форма редактирования --}}
            <div class="lg:col-span-2">
                {{-- Табы --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100">
                        <nav class="flex -mb-px overflow-x-auto">
                            <button @click="activeTab = 'personal'" 
                                :class="activeTab === 'personal' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 min-w-[120px] py-4 px-4 text-center border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <i data-lucide="user" class="w-4 h-4 inline-block mr-1.5"></i>
                                Личные данные
                            </button>
                            <button @click="activeTab = 'body'" 
                                :class="activeTab === 'body' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 min-w-[120px] py-4 px-4 text-center border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <i data-lucide="activity" class="w-4 h-4 inline-block mr-1.5"></i>
                                Тело и цели
                            </button>
                            <button @click="activeTab = 'preferences'" 
                                :class="activeTab === 'preferences' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 min-w-[120px] py-4 px-4 text-center border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <i data-lucide="utensils" class="w-4 h-4 inline-block mr-1.5"></i>
                                Питание
                            </button>
                            <button @click="activeTab = 'notifications'" 
                                :class="activeTab === 'notifications' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 min-w-[120px] py-4 px-4 text-center border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <i data-lucide="bell" class="w-4 h-4 inline-block mr-1.5"></i>
                                Уведомления
                            </button>
                        </nav>
                    </div>

                    <form action="{{ route('dashboard.profile.update') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')

                        {{-- Таб: Личные данные --}}
                        <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Имя <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="+7 (999) 123-45-67"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" 
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    @error('birth_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">О себе</label>
                                    <textarea name="bio" id="bio" rows="3" placeholder="Расскажите немного о себе и своих целях..."
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Таб: Тело и цели --}}
                        <div x-show="activeTab === 'body'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                                    <select name="gender" id="gender" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <option value="">Не указан</option>
                                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Мужской</option>
                                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Женский</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="activity_level" class="block text-sm font-medium text-gray-700 mb-2">Уровень активности</label>
                                    <select name="activity_level" id="activity_level" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <option value="">Не указан</option>
                                        <option value="sedentary" {{ old('activity_level', $user->activity_level) === 'sedentary' ? 'selected' : '' }}>Сидячий образ жизни</option>
                                        <option value="lightly_active" {{ old('activity_level', $user->activity_level) === 'lightly_active' ? 'selected' : '' }}>Легкая активность (1-2 тренировки/нед)</option>
                                        <option value="moderately_active" {{ old('activity_level', $user->activity_level) === 'moderately_active' ? 'selected' : '' }}>Умеренная активность (3-4 тренировки/нед)</option>
                                        <option value="very_active" {{ old('activity_level', $user->activity_level) === 'very_active' ? 'selected' : '' }}>Высокая активность (5-6 тренировок/нед)</option>
                                        <option value="extremely_active" {{ old('activity_level', $user->activity_level) === 'extremely_active' ? 'selected' : '' }}>Очень высокая (ежедневные тренировки)</option>
                                    </select>
                                    @error('activity_level')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Рост</label>
                                    <div class="relative">
                                        <input type="number" name="height" id="height" value="{{ old('height', $user->height) }}" min="100" max="250" step="0.1" placeholder="170"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 pr-12">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">см</span>
                                    </div>
                                    @error('height')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Текущий вес</label>
                                    <div class="relative">
                                        <input type="number" name="weight" id="weight" value="{{ old('weight', $user->weight) }}" min="30" max="300" step="0.1" placeholder="70"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 pr-12">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">кг</span>
                                    </div>
                                    @error('weight')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="target_weight" class="block text-sm font-medium text-gray-700 mb-2">
                                        Целевой вес
                                        <span class="text-gray-400 font-normal">(необязательно)</span>
                                    </label>
                                    <div class="relative max-w-xs">
                                        <input type="number" name="target_weight" id="target_weight" value="{{ old('target_weight', $user->target_weight) }}" min="30" max="300" step="0.1" placeholder="65"
                                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 pr-12">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">кг</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Укажите желаемый вес для отслеживания прогресса</p>
                                    @error('target_weight')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Информационный блок --}}
                            <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="flex gap-3">
                                    <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-medium mb-1">Зачем нужны эти данные?</p>
                                        <p>Мы используем ваши физические параметры для расчета оптимальной калорийности и подбора персонализированных рекомендаций по питанию.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Таб: Питание --}}
                        <div x-show="activeTab === 'preferences'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="space-y-8">
                                {{-- Пищевые предпочтения --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Тип питания</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($dietaryOptions as $value => $label)
                                            <label class="relative flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all hover:border-green-200 has-[:checked]:border-green-500 has-[:checked]:bg-green-50 border-gray-200"
                                                x-data="{ checked: {{ in_array($value, old('dietary_preferences', $user->dietary_preferences ?? [])) ? 'true' : 'false' }} }">
                                                <input type="checkbox" name="dietary_preferences[]" value="{{ $value }}" 
                                                    x-model="checked"
                                                    class="sr-only">
                                                <span class="w-5 h-5 rounded-md border-2 mr-3 flex items-center justify-center transition-all"
                                                    :class="checked ? 'border-green-500 bg-green-500' : 'border-gray-300'">
                                                    <svg x-show="checked" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </span>
                                                <span class="text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Аллергии --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Аллергии и непереносимости
                                        <span class="text-gray-400 font-normal">(исключим из индивидуального меню)</span>
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($allergyOptions as $value => $label)
                                            <label class="relative flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all hover:border-red-200 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 border-gray-200"
                                                x-data="{ checked: {{ in_array($value, old('allergies', $user->allergies ?? [])) ? 'true' : 'false' }} }">
                                                <input type="checkbox" name="allergies[]" value="{{ $value }}" 
                                                    x-model="checked"
                                                    class="sr-only">
                                                <span class="w-5 h-5 rounded-md border-2 mr-3 flex items-center justify-center transition-all"
                                                    :class="checked ? 'border-red-500 bg-red-500' : 'border-gray-300'">
                                                    <svg x-show="checked" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </span>
                                                <span class="text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Таб: Уведомления --}}
                        <div x-show="activeTab === 'notifications'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
                                     x-data="{ checked: {{ old('email_notifications', $user->email_notifications) ? 'true' : 'false' }} }">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="mail" class="w-5 h-5 text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Email-уведомления</div>
                                            <div class="text-sm text-gray-500">Новые меню, рецепты и специальные предложения</div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="email_notifications" :value="checked ? '1' : '0'">
                                    <button type="button" @click="checked = !checked" 
                                        class="w-12 h-7 rounded-full transition-colors duration-200 relative flex-shrink-0"
                                        :class="checked ? 'bg-green-500' : 'bg-gray-300'">
                                        <span class="absolute top-1 w-5 h-5 bg-white rounded-full shadow-md transition-[left] duration-200"
                                            x-bind:style="checked ? 'left: 1.25rem' : 'left: 0.25rem'"></span>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
                                     x-data="{ checked: {{ old('push_notifications', $user->push_notifications) ? 'true' : 'false' }} }">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i data-lucide="bell" class="w-5 h-5 text-purple-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Push-уведомления</div>
                                            <div class="text-sm text-gray-500">Напоминания о приемах пищи и списках покупок</div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="push_notifications" :value="checked ? '1' : '0'">
                                    <button type="button" @click="checked = !checked" 
                                        class="w-12 h-7 rounded-full transition-colors duration-200 relative flex-shrink-0"
                                        :class="checked ? 'bg-green-500' : 'bg-gray-300'">
                                        <span class="absolute top-1 w-5 h-5 bg-white rounded-full shadow-md transition-[left] duration-200"
                                            x-bind:style="checked ? 'left: 1.25rem' : 'left: 0.25rem'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100">
                                <div class="flex gap-3">
                                    <i data-lucide="shield-check" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5"></i>
                                    <div class="text-sm text-amber-700">
                                        <p>Мы не передаем ваши данные третьим лицам и не рассылаем спам. Вы можете отписаться в любой момент.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Кнопка сохранения --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row gap-4">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition shadow-lg shadow-green-500/25">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                Сохранить изменения
                            </button>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                                Вернуться в ЛК
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>
