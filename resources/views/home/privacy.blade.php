@extends('layouts.public')

@section('title', 'Политика конфиденциальности — RawPlan')
@section('description', 'Политика конфиденциальности сервиса RawPlan. Узнайте, как мы собираем, используем и защищаем ваши персональные данные.')
@section('keywords', 'политика конфиденциальности, персональные данные, защита данных, RawPlan')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-6">Политика конфиденциальности</h1>
        <p class="text-xl text-green-100 max-w-2xl mx-auto">
            Последнее обновление: {{ date('d.m.Y') }}
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            
            <div class="bg-green-50 border border-green-100 rounded-2xl p-6 mb-8">
                <p class="text-gray-700 m-0">
                    Настоящая Политика конфиденциальности описывает, как ООО «РоуПлан» (далее — «мы», «нас», «RawPlan») 
                    собирает, использует и защищает информацию, которую вы предоставляете при использовании сайта rawplan.ru.
                </p>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">1. Какие данные мы собираем</h2>
            <p class="text-gray-600 mb-4">Мы можем собирать следующие типы информации:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                <li><strong>Персональные данные:</strong> имя, адрес электронной почты, номер телефона (при регистрации)</li>
                <li><strong>Данные профиля:</strong> пол, возраст, рост, вес, цели по питанию (для персональных планов)</li>
                <li><strong>Платёжные данные:</strong> информация о транзакциях (мы не храним данные банковских карт)</li>
                <li><strong>Технические данные:</strong> IP-адрес, тип браузера, информация об устройстве</li>
                <li><strong>Данные об использовании:</strong> страницы, которые вы посещаете, действия на сайте</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">2. Как мы используем ваши данные</h2>
            <p class="text-gray-600 mb-4">Мы используем собранную информацию для:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                <li>Предоставления доступа к сервису и его функциям</li>
                <li>Обработки платежей и управления подписками</li>
                <li>Персонализации планов питания и рекомендаций</li>
                <li>Отправки уведомлений о новых меню и обновлениях</li>
                <li>Улучшения качества сервиса и пользовательского опыта</li>
                <li>Ответов на ваши обращения в службу поддержки</li>
                <li>Соблюдения требований законодательства</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">3. Защита данных</h2>
            <p class="text-gray-600 mb-6">
                Мы применяем современные технические и организационные меры для защиты ваших персональных данных 
                от несанкционированного доступа, изменения, раскрытия или уничтожения. Все данные передаются 
                по защищённому протоколу HTTPS. Платёжные операции обрабатываются сертифицированными платёжными 
                системами (ЮKassa, CloudPayments).
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">4. Передача данных третьим лицам</h2>
            <p class="text-gray-600 mb-4">Мы не продаём и не передаём ваши персональные данные третьим лицам, за исключением:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                <li>Платёжных систем для обработки транзакций</li>
                <li>Сервисов аналитики (в обезличенном виде)</li>
                <li>Случаев, предусмотренных законодательством РФ</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">5. Файлы cookie</h2>
            <p class="text-gray-600 mb-6">
                Мы используем файлы cookie для обеспечения работы сайта, сохранения ваших настроек и анализа 
                использования сервиса. Вы можете отключить cookie в настройках браузера, однако это может 
                повлиять на функциональность сайта.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">6. Ваши права</h2>
            <p class="text-gray-600 mb-4">Вы имеете право:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                <li>Получить информацию о хранящихся у нас данных</li>
                <li>Запросить исправление неточных данных</li>
                <li>Запросить удаление ваших данных</li>
                <li>Отозвать согласие на обработку данных</li>
                <li>Отказаться от рассылки уведомлений</li>
            </ul>
            <p class="text-gray-600 mb-6">
                Для реализации этих прав свяжитесь с нами по адресу 
                <a href="mailto:privacy@rawplan.ru" class="text-green-600 hover:text-green-700">privacy@rawplan.ru</a>.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">7. Хранение данных</h2>
            <p class="text-gray-600 mb-6">
                Мы храним ваши персональные данные в течение срока действия вашей учётной записи и в течение 
                3 лет после её удаления для соблюдения требований законодательства. Платёжные данные хранятся 
                в соответствии с требованиями налогового законодательства.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">8. Изменения политики</h2>
            <p class="text-gray-600 mb-6">
                Мы можем обновлять данную Политику конфиденциальности. О существенных изменениях мы уведомим 
                вас по электронной почте или через уведомление на сайте. Рекомендуем периодически проверять 
                эту страницу.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">9. Контакты</h2>
            <p class="text-gray-600 mb-6">
                Если у вас есть вопросы о данной Политике конфиденциальности, свяжитесь с нами:
            </p>
            <div class="bg-gray-50 rounded-2xl p-6">
                <p class="text-gray-700 mb-2"><strong>ООО «РоуПлан»</strong></p>
                <p class="text-gray-600 mb-1">Email: <a href="mailto:privacy@rawplan.ru" class="text-green-600 hover:text-green-700">privacy@rawplan.ru</a></p>
                <p class="text-gray-600">Адрес: г. Москва, ул. Примерная, д. 1</p>
            </div>

        </div>
    </div>
</section>
@endsection
