<x-app-layout>
    @section('title', 'Добро пожаловать в RawPlan')

    <div class="min-h-[80vh] flex items-center justify-center px-4">
        <div class="max-w-2xl w-full">
            {{-- Прогресс-бар --}}
            <div class="flex items-center justify-center gap-2 mb-8">
                <div class="w-10 h-1.5 rounded-full bg-green-500"></div>
                <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
                <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
                <span class="ml-2 text-xs text-gray-400">Шаг 1 из 3</span>
            </div>

            {{-- Карточка приветствия --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                {{-- Градиентный хедер --}}
                <div class="bg-gradient-to-br from-green-500 via-emerald-500 to-teal-600 px-6 sm:px-10 py-10 text-white text-center">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <i data-lucide="sparkles" class="w-10 h-10 text-white"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold mb-2">
                        Привет, {{ $user->name }}! 👋
                    </h1>
                    <p class="text-green-100 text-base sm:text-lg">
                        Добро пожаловать в RawPlan — ваш персональный помощник в здоровом питании
                    </p>
                </div>

                {{-- Преимущества --}}
                <div class="px-6 sm:px-10 py-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 text-center">
                        Что вас ждёт:
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-start gap-3 p-4 bg-green-50 rounded-xl">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="utensils" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 text-sm">Готовые меню</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Сбалансированное питание на каждый день 1200–1400 ккал</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-xl">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="chef-hat" class="w-5 h-5 text-amber-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 text-sm">Простые рецепты</h3>
                                <p class="text-xs text-gray-500 mt-0.5">С точным КБЖУ и пошаговыми инструкциями</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="shopping-cart" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 text-sm">Списки покупок</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Автоматическая генерация на неделю</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-purple-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="target" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 text-sm">Результат</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Видимые изменения уже через 2 недели</p>
                            </div>
                        </div>
                    </div>

                    {{-- Бонус --}}
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 mb-8 text-center">
                        <div class="flex items-center justify-center gap-2 mb-1">
                            <i data-lucide="gift" class="w-5 h-5 text-green-600"></i>
                            <span class="font-semibold text-green-800">Подарок для вас</span>
                        </div>
                        <p class="text-sm text-green-700">
                            <strong>7 дней бесплатного доступа</strong> — активируется автоматически после короткой анкеты
                        </p>
                    </div>

                    {{-- Кнопка --}}
                    <div class="text-center">
                        <a href="{{ route('onboarding.survey') }}"
                           class="inline-flex items-center gap-2 px-8 py-3.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold text-base transition shadow-lg shadow-green-500/25 hover:shadow-green-500/40">
                            Начать
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                        <p class="text-xs text-gray-400 mt-3">Займёт меньше минуты</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
