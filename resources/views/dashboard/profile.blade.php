<x-app-layout>
    <div class="max-w-4xl mx-auto">
        {{-- Заголовок --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Мой профиль</h1>
                <p class="text-gray-600">Управляйте своими данными</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('dashboard.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Основная информация --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-green-500"></i>
                    Основная информация
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="+7 (999) 123-45-67"
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
                </div>
            </div>

            {{-- Физические параметры --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-blue-500"></i>
                    Физические параметры
                </h2>
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
                            <option value="light" {{ old('activity_level', $user->activity_level) === 'light' ? 'selected' : '' }}>Легкая активность</option>
                            <option value="moderate" {{ old('activity_level', $user->activity_level) === 'moderate' ? 'selected' : '' }}>Умеренная активность</option>
                            <option value="active" {{ old('activity_level', $user->activity_level) === 'active' ? 'selected' : '' }}>Высокая активность</option>
                            <option value="very_active" {{ old('activity_level', $user->activity_level) === 'very_active' ? 'selected' : '' }}>Очень высокая активность</option>
                        </select>
                        @error('activity_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                        <input type="number" name="height" id="height" value="{{ old('height', $user->height) }}" min="100" max="250" placeholder="170"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $user->weight) }}" min="30" max="300" placeholder="70"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Кнопки --}}
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition shadow-lg shadow-green-500/25">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Сохранить изменения
                </button>
                <a href="{{ route('profile.show') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    Настройки аккаунта
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
