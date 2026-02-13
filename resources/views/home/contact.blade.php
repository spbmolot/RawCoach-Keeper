@extends('layouts.public')

@section('title', 'Контакты — RawPlan')
@section('description', 'Свяжитесь с командой RawPlan. Мы всегда рады помочь с вопросами о планах питания, подписках и технической поддержке.')
@section('keywords', 'контакты, RawPlan, поддержка, обратная связь, помощь')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4 sm:mb-6">Контакты</h1>
        <p class="text-base sm:text-xl text-green-100 max-w-2xl mx-auto">
            Есть вопросы? Мы всегда рады помочь!
        </p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-10 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 sm:gap-16">
            <!-- Contact Form -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Напишите нам</h2>
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Ваше имя</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="Как к вам обращаться?">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="your@email.com">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Тема</label>
                        <select name="subject" id="subject" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="">Выберите тему</option>
                            <option value="Вопрос о подписке" {{ old('subject') == 'Вопрос о подписке' ? 'selected' : '' }}>Вопрос о подписке</option>
                            <option value="Техническая поддержка" {{ old('subject') == 'Техническая поддержка' ? 'selected' : '' }}>Техническая поддержка</option>
                            <option value="Предложение о сотрудничестве" {{ old('subject') == 'Предложение о сотрудничестве' ? 'selected' : '' }}>Предложение о сотрудничестве</option>
                            <option value="Отзыв о сервисе" {{ old('subject') == 'Отзыв о сервисе' ? 'selected' : '' }}>Отзыв о сервисе</option>
                            <option value="Другое" {{ old('subject') == 'Другое' ? 'selected' : '' }}>Другое</option>
                        </select>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Сообщение</label>
                        <textarea name="message" id="message" rows="5" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition resize-none"
                            placeholder="Опишите ваш вопрос...">{{ old('message') }}</textarea>
                    </div>
                    <x-recaptcha action="contact" />

                    <button type="submit" 
                        class="w-full px-6 py-4 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/25 flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Отправить сообщение
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Другие способы связи</h2>
                
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                                <p class="text-gray-600 mb-2">Для общих вопросов и поддержки</p>
                                <a href="mailto:support@rawplan.ru" class="text-green-600 hover:text-green-700 font-medium">
                                    support@rawplan.ru
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i data-lucide="send" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Telegram</h3>
                                <p class="text-gray-600 mb-2">Быстрые ответы и новости</p>
                                <a href="https://t.me/rawplan" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700 font-medium">
                                    @rawplan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Время ответа</h3>
                                <p class="text-gray-600">
                                    Мы отвечаем на все обращения в течение 24 часов в рабочие дни.
                                    В выходные — в течение 48 часов.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Link -->
                <div class="mt-8 p-6 bg-green-50 rounded-2xl border border-green-100">
                    <h3 class="font-semibold text-gray-900 mb-2">Частые вопросы</h3>
                    <p class="text-gray-600 mb-4">
                        Возможно, ответ на ваш вопрос уже есть в разделе FAQ на главной странице.
                    </p>
                    <a href="{{ route('home') }}#faq" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium">
                        Перейти к FAQ
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
