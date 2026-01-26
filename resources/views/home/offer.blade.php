@extends('layouts.public')

@section('title', 'Публичная оферта — RawPlan')
@section('description', 'Публичная оферта на оказание услуг сервиса RawPlan. Условия предоставления доступа к планам питания.')
@section('keywords', 'оферта, договор, условия, RawPlan, подписка')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-6">Публичная оферта</h1>
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
                    Настоящий документ является официальным предложением (публичной офертой) ООО «РоуПлан» 
                    (далее — «Исполнитель») заключить договор на оказание информационных услуг на условиях, 
                    изложенных ниже.
                </p>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">1. Термины и определения</h2>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                <li><strong>Оферта</strong> — настоящий документ, размещённый в сети Интернет по адресу rawplan.ru/offer</li>
                <li><strong>Акцепт</strong> — полное и безоговорочное принятие Оферты путём оплаты услуг</li>
                <li><strong>Заказчик</strong> — физическое лицо, акцептовавшее Оферту</li>
                <li><strong>Исполнитель</strong> — ООО «РоуПлан»</li>
                <li><strong>Услуги</strong> — предоставление доступа к контенту сервиса RawPlan</li>
                <li><strong>Подписка</strong> — оплаченный период доступа к Услугам</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">2. Предмет договора</h2>
            <p class="text-gray-600 mb-4">
                2.1. Исполнитель обязуется предоставить Заказчику доступ к информационным материалам сервиса 
                RawPlan (планы питания, рецепты, списки покупок), а Заказчик обязуется оплатить Услуги.
            </p>
            <p class="text-gray-600 mb-6">
                2.2. Объём и стоимость Услуг определяются выбранным тарифным планом.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">3. Тарифы и оплата</h2>
            <p class="text-gray-600 mb-4">
                3.1. Актуальные тарифы размещены на странице <a href="{{ route('plans.index') }}" class="text-green-600 hover:text-green-700">rawplan.ru/plans</a>.
            </p>
            <p class="text-gray-600 mb-4">
                3.2. Оплата производится в рублях РФ банковской картой или иным доступным способом.
            </p>
            <p class="text-gray-600 mb-4">
                3.3. Моментом оплаты считается зачисление денежных средств на счёт Исполнителя.
            </p>
            <p class="text-gray-600 mb-6">
                3.4. Подписка активируется автоматически после подтверждения оплаты.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">4. Пробный период</h2>
            <p class="text-gray-600 mb-4">
                4.1. Новым пользователям предоставляется бесплатный пробный период 7 дней.
            </p>
            <p class="text-gray-600 mb-4">
                4.2. Пробный период предоставляется однократно на одного пользователя.
            </p>
            <p class="text-gray-600 mb-6">
                4.3. По истечении пробного периода подписка автоматически продлевается на платной основе, 
                если не была отменена.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">5. Автопродление подписки</h2>
            <p class="text-gray-600 mb-4">
                5.1. Подписка продлевается автоматически по окончании оплаченного периода.
            </p>
            <p class="text-gray-600 mb-4">
                5.2. Списание средств происходит за 1 день до окончания текущего периода.
            </p>
            <p class="text-gray-600 mb-4">
                5.3. Заказчик может отключить автопродление в личном кабинете в любой момент.
            </p>
            <p class="text-gray-600 mb-6">
                5.4. При отключении автопродления доступ сохраняется до конца оплаченного периода.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">6. Возврат средств</h2>
            <p class="text-gray-600 mb-4">
                6.1. Возврат средств возможен в течение 14 дней с момента первой оплаты, если Заказчик 
                не воспользовался Услугами.
            </p>
            <p class="text-gray-600 mb-4">
                6.2. Для оформления возврата необходимо обратиться в службу поддержки: 
                <a href="mailto:support@rawplan.ru" class="text-green-600 hover:text-green-700">support@rawplan.ru</a>.
            </p>
            <p class="text-gray-600 mb-4">
                6.3. Возврат осуществляется тем же способом, которым была произведена оплата.
            </p>
            <p class="text-gray-600 mb-6">
                6.4. Срок возврата — до 10 рабочих дней с момента одобрения заявки.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">7. Права и обязанности сторон</h2>
            <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">7.1. Исполнитель обязуется:</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                <li>Предоставить доступ к Услугам после оплаты</li>
                <li>Обеспечить работоспособность сервиса</li>
                <li>Оказывать техническую поддержку</li>
                <li>Уведомлять о существенных изменениях условий</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">7.2. Заказчик обязуется:</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2 mb-6">
                <li>Своевременно оплачивать Услуги</li>
                <li>Не передавать доступ третьим лицам</li>
                <li>Не копировать и не распространять контент</li>
                <li>Соблюдать условия Пользовательского соглашения</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">8. Ответственность сторон</h2>
            <p class="text-gray-600 mb-4">
                8.1. Стороны несут ответственность в соответствии с законодательством РФ.
            </p>
            <p class="text-gray-600 mb-4">
                8.2. Исполнитель не несёт ответственности за результаты применения планов питания.
            </p>
            <p class="text-gray-600 mb-6">
                8.3. Материалы сервиса не являются медицинскими рекомендациями.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">9. Срок действия и расторжение</h2>
            <p class="text-gray-600 mb-4">
                9.1. Договор вступает в силу с момента Акцепта и действует до полного исполнения обязательств.
            </p>
            <p class="text-gray-600 mb-4">
                9.2. Заказчик вправе расторгнуть договор, отменив подписку в личном кабинете.
            </p>
            <p class="text-gray-600 mb-6">
                9.3. Исполнитель вправе расторгнуть договор при нарушении Заказчиком условий Оферты.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">10. Реквизиты Исполнителя</h2>
            <div class="bg-gray-50 rounded-2xl p-6">
                <p class="text-gray-700 mb-2"><strong>ООО «РоуПлан»</strong></p>
                <p class="text-gray-600 mb-1">ИНН: 7700000000</p>
                <p class="text-gray-600 mb-1">ОГРН: 1177700000000</p>
                <p class="text-gray-600 mb-1">Юридический адрес: г. Москва, ул. Примерная, д. 1</p>
                <p class="text-gray-600 mb-1">Email: <a href="mailto:support@rawplan.ru" class="text-green-600 hover:text-green-700">support@rawplan.ru</a></p>
            </div>

        </div>
    </div>
</section>
@endsection
