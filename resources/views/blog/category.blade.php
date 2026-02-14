@extends('layouts.public')

@section('title', ($category->meta_title ?: $category->name . ' — Блог RawPlan'))
@section('description', $category->meta_description ?: 'Статьи в категории «' . $category->name . '». Полезная информация о правильном питании и похудении.')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-600 to-emerald-700 text-white py-10 sm:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm text-green-200 mb-4">
                <a href="{{ route('blog.index') }}" class="hover:text-white transition">Блог</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">{{ $category->name }}</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-bold">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-3 text-green-100 text-lg max-w-2xl">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <div class="lg:col-span-3">
                @if($posts->isEmpty())
                    <div class="text-center py-16">
                        <p class="text-gray-500 text-lg">В этой категории пока нет статей</p>
                        <a href="{{ route('blog.index') }}" class="mt-4 inline-block text-green-600 hover:text-green-700 font-medium">← Все статьи</a>
                    </div>
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($posts as $post)
                            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                                <a href="{{ route('blog.show', $post->slug) }}" class="block">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-green-100 to-emerald-50 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="p-5">
                                    <h2 class="mb-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="text-lg font-bold text-gray-900 hover:text-green-600 transition line-clamp-2">{{ $post->title }}</a>
                                    </h2>
                                    <p class="text-gray-500 text-sm line-clamp-3 mb-4">{{ $post->getExcerptOrTruncatedBody(120) }}</p>
                                    <div class="flex items-center justify-between text-xs text-gray-400">
                                        <span>{{ $post->published_at->format('d.m.Y') }}</span>
                                        <span>{{ $post->reading_time }} мин</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $posts->links() }}</div>
                @endif
            </div>

            {{-- Сайдбар с категориями --}}
            <aside class="mt-8 lg:mt-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Категории</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}" 
                                   class="flex items-center justify-between text-sm py-1 transition {{ $cat->id === $category->id ? 'text-green-600 font-semibold' : 'text-gray-600 hover:text-green-600' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
