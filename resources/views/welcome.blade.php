<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RawCoach - Сервис по похудению</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-gray-900">
<div class="flex flex-col min-h-screen">

    <!-- Hero with day preview -->
    <section class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1546484959-f9a77a5c7f1d?auto=format&fit=crop&w=1600&q=80');">
        <div class="bg-black/50">
            <div class="container mx-auto px-6 py-32 text-center text-white">
                <h1 class="text-5xl font-bold mb-6">Снижай вес вкусно и безопасно</h1>
                <p class="text-xl mb-8">Персональные меню 1200–1400 ккал и списки покупок каждую неделю.</p>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-green-500 hover:bg-green-600 rounded-md font-semibold">Перейти в кабинет</a>
                @else
                    <a href="#" class="px-8 py-3 bg-green-500 hover:bg-green-600 rounded-md font-semibold" onclick="console.log('cta-click')">Попробовать бесплатно</a>
                @endauth

                <div class="mt-12 inline-block bg-white text-gray-800 rounded-lg shadow p-6 text-left max-w-sm mx-auto">
                    <h3 class="font-semibold mb-2">Превью дня</h3>
                    <p class="mb-1">Завтрак: Овсянка с ягодами — 250 г / 350 ккал</p>
                    <p class="mb-1">Обед: Куриная грудка с рисом — 300 г / 420 ккал</p>
                    <p>Ужин: Рыба на пару — 200 г / 280 ккал</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Как это работает</h2>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div class="mb-4 text-4xl text-green-500">1</div>
                    <p class="font-semibold">Зарегистрируйтесь</p>
                    <p class="text-gray-600">Создайте аккаунт за минуту.</p>
                </div>
                <div class="text-center">
                    <div class="mb-4 text-4xl text-green-500">2</div>
                    <p class="font-semibold">Выберите тариф</p>
                    <p class="text-gray-600">Месяц, год или индивидуальный план.</p>
                </div>
                <div class="text-center">
                    <div class="mb-4 text-4xl text-green-500">3</div>
                    <p class="font-semibold">Получайте меню</p>
                    <p class="text-gray-600">Свежие рецепты каждый месяц.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inside subscription -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Что внутри подписки</h2>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">Ежедневные меню</h3>
                    <p class="text-gray-600">Завтрак, обед и ужин с точным весом и калориями.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">1200–1400 ккал</h3>
                    <p class="text-gray-600">Оптимальный диапазон для похудения.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">Списки покупок</h3>
                    <p class="text-gray-600">Готовые списки на неделю в PDF и Excel.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing with toggle -->
    <section class="py-20 bg-white" id="pricing">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-6">Тарифы</h2>
            <div class="flex justify-center mb-12">
                <button id="monthlyBtn" class="px-4 py-2 bg-green-500 text-white rounded-l">Месяц</button>
                <button id="yearlyBtn" class="px-4 py-2 bg-green-500/30 text-green-700 rounded-r">Год -20%</button>
            </div>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="border rounded-lg p-8 text-center">
                    <h3 class="text-xl font-semibold mb-4">Базовый</h3>
                    <p class="text-4xl font-bold mb-4"><span class="price" data-month="990" data-year="7990">990</span>₽/<span class="period">мес</span></p>
                    <ul class="mb-6 text-gray-600">
                        <li>Доступ к меню</li>
                        <li>Списки покупок</li>
                    </ul>
                    <a class="block px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600" href="#" onclick="console.log('plan-basic')">Оформить</a>
                </div>
                <div class="border-2 border-green-500 rounded-lg p-8 text-center">
                    <h3 class="text-xl font-semibold mb-4">Премиум</h3>
                    <p class="text-4xl font-bold mb-4"><span class="price" data-month="1490" data-year="11900">1490</span>₽/<span class="period">мес</span></p>
                    <ul class="mb-6 text-gray-600">
                        <li>Личный план</li>
                        <li>Поддержка</li>
                        <li>Доступ к архиву</li>
                    </ul>
                    <a class="block px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600" href="#" onclick="console.log('plan-premium')">Оформить</a>
                </div>
                <div class="border rounded-lg p-8 text-center">
                    <h3 class="text-xl font-semibold mb-4">Индивидуальный</h3>
                    <p class="text-4xl font-bold mb-4">По запросу</p>
                    <ul class="mb-6 text-gray-600">
                        <li>Персональный нутрициолог</li>
                        <li>План под ваши цели</li>
                    </ul>
                    <a class="block px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600" href="#" onclick="console.log('plan-custom')">Связаться</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Отзывы</h2>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="mb-4 text-gray-700">"С RawCoach я похудела на 10 кг за 3 месяца!"</p>
                    <p class="font-semibold">Анна</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="mb-4 text-gray-700">"Простое приложение и отличные тренеры."</p>
                    <p class="font-semibold">Игорь</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="mb-4 text-gray-700">"Мой путь к здоровью стал понятным и лёгким."</p>
                    <p class="font-semibold">Мария</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">FAQ</h2>
            <div class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <h3 class="font-semibold">Можно ли отменить подписку?</h3>
                    <p class="text-gray-600">Да, вы можете отказаться в любой момент в личном кабинете.</p>
                </div>
                <div class="mb-6">
                    <h3 class="font-semibold">Когда я получу меню?</h3>
                    <p class="text-gray-600">Меню на месяц приходит сразу после оплаты.</p>
                </div>
                <div class="mb-6">
                    <h3 class="font-semibold">Есть ли пробный период?</h3>
                    <p class="text-gray-600">Мы предоставляем 7 дней бесплатного доступа.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ad slot -->
    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="border border-dashed border-gray-400 p-8 text-center text-gray-500">
                Рекламный слот
            </div>
        </div>
    </section>

    <!-- CTA repeat -->
    <section class="py-20 bg-green-500 text-white text-center">
        <h2 class="text-3xl font-bold mb-4">Готовы начать?</h2>
        <p class="mb-6">Присоединяйтесь к RawCoach и измените свою жизнь уже сегодня.</p>
        @auth
            <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-white text-green-600 font-semibold rounded-md">Перейти в кабинет</a>
        @else
            <a href="#" class="px-8 py-3 bg-white text-green-600 font-semibold rounded-md" onclick="console.log('cta-footer')">Создать аккаунт</a>
        @endauth
    </section>

    <footer class="py-6 text-center text-gray-500 text-sm">
        © {{ date('Y') }} RawCoach. Все права защищены.
    </footer>
</div>

<script>
const monthlyBtn = document.getElementById('monthlyBtn');
const yearlyBtn = document.getElementById('yearlyBtn');
const prices = document.querySelectorAll('.price');
const periods = document.querySelectorAll('.period');
if (monthlyBtn && yearlyBtn) {
    monthlyBtn.addEventListener('click', () => {
        monthlyBtn.classList.add('bg-green-500','text-white');
        monthlyBtn.classList.remove('bg-green-500/30','text-green-700');
        yearlyBtn.classList.add('bg-green-500/30','text-green-700');
        yearlyBtn.classList.remove('bg-green-500','text-white');
        prices.forEach(p => p.textContent = p.dataset.month);
        periods.forEach(p => p.textContent = 'мес');
    });
    yearlyBtn.addEventListener('click', () => {
        yearlyBtn.classList.add('bg-green-500','text-white');
        yearlyBtn.classList.remove('bg-green-500/30','text-green-700');
        monthlyBtn.classList.add('bg-green-500/30','text-green-700');
        monthlyBtn.classList.remove('bg-green-500','text-white');
        prices.forEach(p => p.textContent = p.dataset.year);
        periods.forEach(p => p.textContent = 'год');
    });
}
</script>

</body>
</html>

