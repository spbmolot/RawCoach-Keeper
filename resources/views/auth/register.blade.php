<x-guest-layout>
    <x-authentication-card>
        <!-- Заголовок формы -->
        <div class="text-center mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Регистрация</h2>
            <p class="text-sm text-gray-500 mt-1">Создайте аккаунт для доступа к планам</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="name" value="Имя" class="text-sm font-medium text-gray-700" />
                <x-input id="name" 
                    class="block mt-1.5 w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base py-2.5 sm:py-3" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    placeholder="Ваше имя"
                    required 
                    autofocus 
                    autocomplete="name" />
            </div>

            <div>
                <x-label for="email" value="Email" class="text-sm font-medium text-gray-700" />
                <x-input id="email" 
                    class="block mt-1.5 w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base py-2.5 sm:py-3" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    placeholder="your@email.com"
                    required 
                    autocomplete="username" />
            </div>

            <div>
                <x-label for="password" value="Пароль" class="text-sm font-medium text-gray-700" />
                <x-input id="password" 
                    class="block mt-1.5 w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base py-2.5 sm:py-3" 
                    type="password" 
                    name="password" 
                    placeholder="Минимум 8 символов"
                    required 
                    autocomplete="new-password" />
            </div>

            <div>
                <x-label for="password_confirmation" value="Подтверждение пароля" class="text-sm font-medium text-gray-700" />
                <x-input id="password_confirmation" 
                    class="block mt-1.5 w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base py-2.5 sm:py-3" 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Повторите пароль"
                    required 
                    autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="flex items-start">
                    <x-checkbox name="terms" id="terms" required class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500" />
                    <label for="terms" class="ms-2 text-sm text-gray-600 leading-relaxed cursor-pointer">
                        Я принимаю 
                        <a target="_blank" href="{{ route('terms.show') }}" class="text-green-600 hover:text-green-700 font-medium">условия использования</a> 
                        и 
                        <a target="_blank" href="{{ route('policy.show') }}" class="text-green-600 hover:text-green-700 font-medium">политику конфиденциальности</a>
                    </label>
                </div>
            @endif

            <x-recaptcha action="register" />

            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-semibold py-2.5 sm:py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm sm:text-base">
                Зарегистрироваться
            </button>
        </form>

        <!-- Ссылка на вход -->
        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500">
                Уже есть аккаунт? 
                <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold transition-colors">
                    Войти
                </a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>
