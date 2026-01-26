<footer class="bg-gray-900 text-gray-400 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-12 mb-12">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-xl hero-gradient flex items-center justify-center">
                        <i data-lucide="salad" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-white">RawPlan</span>
                </a>
                <p class="text-gray-400 max-w-sm">Готовые планы питания для здорового похудения. Меню, рецепты и списки покупок — всё в одном месте.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Навигация</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}#features" class="hover:text-white transition">Возможности</a></li>
                    <li><a href="{{ route('plans.index') }}" class="hover:text-white transition">Тарифы</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">О нас</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition">Контакты</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Правовая информация</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Политика конфиденциальности</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">Пользовательское соглашение</a></li>
                    <li><a href="{{ route('offer') }}" class="hover:text-white transition">Оферта</a></li>
                </ul>
            </div>
        </div>
        {{-- Payment badges --}}
        <div class="border-t border-gray-800 pt-8 mb-8">
            <div class="flex flex-wrap items-center justify-center gap-6">
                <div class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="shield-check" class="w-5 h-5 text-green-500"></i>
                    <span class="text-sm">Безопасная оплата</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-gray-800 px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <span class="text-xs font-bold text-blue-400">VISA</span>
                    </div>
                    <div class="bg-gray-800 px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <span class="text-xs font-bold text-orange-400">MC</span>
                    </div>
                    <div class="bg-gray-800 px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <span class="text-xs font-bold text-green-400">МИР</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span class="text-sm">SSL защита</span>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm">© {{ date('Y') }} RawPlan. Все права защищены.</p>
            <div class="flex items-center gap-4">
                <a href="https://t.me/rawplan" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition" title="Telegram">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </a>
                <a href="mailto:support@rawplan.ru" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition" title="Email">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
