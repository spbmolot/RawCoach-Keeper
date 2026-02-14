@extends('layouts.app')

@section('title', 'Блог о правильном питании — RawPlan')
@section('description', 'Полезные статьи о здоровом питании, похудении, рецептах и планировании рациона. Советы нутрициологов и практические рекомендации.')
@section('keywords', 'блог питание, здоровое питание статьи, похудение советы, правильное питание, диета блог')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Hero --}}
    <div class="bg-gradient-to-r from-green-600 to-emerald-700 text-white py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4">Блог о правильном питании</h1>
            <p class="text-lg sm:text-xl text-green-100 max-w-2xl mx-auto mb-8">
                Полезные статьи, советы нутрициологов и практические рекомендации для здорового образа жизни
            </p>
            {{-- Поиск --}}
            <form action="{{ route('blog.index') }}" method="GET" class="max-w-lg mx-auto">
                <div class="relative">
                    <input type="text" name="q" value="{{ $search ?? '' }}" 
                           placeholder="Поиск по статьям..." 
                           class="w-full pl-12 pr-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-300 border-0">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            {{-- Основной контент --}}
            <div class="lg:col-span-3">
                {{-- Активный фильтр --}}
                @if(!empty($tag))
                    <div class="mb-6 flex items-center gap-2">
                        <span class="text-gray-500">Тег:</span>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">{{ $tag }}</span>
                        <a href="{{ route('blog.index') }}" class="text-red-500 hover:text-red-700 text-sm">&times; сбросить</a>
                    </div>
                @endif
                @if(!empty($search))
                    <div class="mb-6 flex items-center gap-2">
                        <span class="text-gray-500">Результаты поиска:</span>
                        <span class="font-semibold">«{{ $search }}»</span>
                        <a href="{{ route('blog.index') }}" class="text-red-500 hover:text-red-700 text-sm">&times; сбросить</a>
                    </div>
                @endif

                @if($posts->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <p class="text-gray-500 text-lg">Статьи не найдены</p>
                    </div>
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($posts as $post)
                            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                                <a href="{{ route('blog.show', $post->slug) }}" class="block">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" 
                                             alt="{{ $post->title }}" 
                                             class="w-full h-48 object-cover group-hover:scale-105 transition duration-300"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-green-100 to-emerald-50 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="p-5">
                                    @if($post->category)
                                        <a href="{{ route('blog.category', $post->category->slug) }}" 
                                           class="text-xs font-semibold text-green-600 uppercase tracking-wider hover:text-green-700">
                                            {{ $post->category->name }}
                                        </a>
                                    @endif
                                    <h2 class="mt-2 mb-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" 
                                           class="text-lg font-bold text-gray-900 hover:text-green-600 transition line-clamp-2">
                                            {{ $post->title }}
                                        </a>
                                    </h2>
                                    <p class="text-gray-500 text-sm line-clamp-3 mb-4">
                                        {{ $post->getExcerptOrTruncatedBody(120) }}
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-400">
                                        <span>{{ $post->published_at->format('d.m.Y') }}</span>
                                        <div class="flex items-center gap-3">
                                            <span>{{ $post->reading_time }} мин</span>
                                            <span>{{ number_format($post->views_count) }} просм.</span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- Сайдбар --}}
            <aside class="mt-8 lg:mt-0 space-y-6">
                {{-- Категории --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Категории</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}" 
                                   class="flex items-center justify-between text-sm text-gray-600 hover:text-green-600 transition py-1">
                                    <span>{{ $cat->name }}</span>
                                    <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Популярные --}}
                @if($popularPosts->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Популярные статьи</h3>
                    <ul class="space-y-3">
                        @foreach($popularPosts as $popular)
                            <li>
                                <a href="{{ route('blog.show', $popular->slug) }}" 
                                   class="text-sm text-gray-700 hover:text-green-600 transition line-clamp-2 block">
                                    {{ $popular->title }}
                                </a>
                                <span class="text-xs text-gray-400">{{ number_format($popular->views_count) }} просм.</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Теги --}}
                @if($allTags->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Теги</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($allTags as $tagName => $tagCount)
                            <a href="{{ route('blog.index', ['tag' => $tagName]) }}" 
                               class="bg-gray-100 hover:bg-green-100 text-gray-600 hover:text-green-700 px-3 py-1 rounded-full text-xs transition {{ ($tag ?? '') === $tagName ? 'bg-green-100 text-green-700 font-semibold' : '' }}">
                                {{ $tagName }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CTA подписка --}}
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 text-white">
                    <h3 class="font-bold mb-2">Попробуйте RawPlan</h3>
                    <p class="text-green-100 text-sm mb-4">Готовые планы питания на каждый день с рецептами и списками покупок</p>
                    <a href="{{ route('plans.index') }}" 
                       class="block text-center bg-white text-green-700 font-semibold py-2 px-4 rounded-lg hover:bg-green-50 transition text-sm">
                        Попробовать бесплатно
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
