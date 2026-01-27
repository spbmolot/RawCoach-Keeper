<x-app-layout>
    <div class="max-w-4xl mx-auto">
        {{-- Заголовок --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Список покупок</h1>
                </div>
                <p class="text-gray-600">Все продукты для вашего меню</p>
            </div>
        </div>

        @if(isset($ingredients) && $ingredients->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-green-50 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="shopping-cart" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Продукты</h3>
                            <p class="text-sm text-gray-500">{{ $ingredients->count() }} позиций</p>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($ingredients as $ingredient)
                        <label class="flex items-center gap-4 p-4 hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-green-500 focus:ring-green-500">
                            <span class="flex-1 text-gray-900">{{ $ingredient['name'] }}</span>
                            <span class="text-gray-500 font-medium">{{ $ingredient['amount'] }} {{ $ingredient['unit'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="shopping-cart" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Список покупок пуст</h3>
                <p class="text-gray-600 mb-6">Список формируется на основе вашего меню</p>
                <a href="{{ route('dashboard.today') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Посмотреть меню
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
