<x-guest-layout>
    <x-authentication-card>
        <!-- Заголовок формы -->
        <div class="text-center mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Вход в аккаунт</h2>
            <p class="text-sm text-gray-500 mt-1">Введите данные для входа</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="email" value="Email" class="text-sm font-medium text-gray-700" />
                <x-input id="email" 
                    class="block mt-1.5 w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base py-2.5 sm:py-3" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    placeholder="your@email.com"
                    required 
                    autofocus 
                    autocomplete="username" />
            </div>

            <div>
                <x-label for="password" value="Пароль" class="text-sm font-medium text-gray-700" />
                <x-input id="password" 
                    class="block mt-1.5 w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base py-2.5 sm:py-3" 
                    type="password" 
                    name="password" 
                    placeholder="••••••••"
                    required 
                    autocomplete="current-password" />
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <label for="remember_me" class="flex items-center cursor-pointer">
                    <x-checkbox id="remember_me" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
                    <span class="ms-2 text-sm text-gray-600">Запомнить меня</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors" href="{{ route('password.request') }}">
                        Забыли пароль?
                    </a>
                @endif
            </div>

            <x-recaptcha action="login" />

            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-semibold py-2.5 sm:py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm sm:text-base">
                Войти
            </button>
        </form>

        <!-- Ссылка на регистрацию -->
        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500">
                Нет аккаунта? 
                <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-semibold transition-colors">
                    Зарегистрироваться
                </a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>
