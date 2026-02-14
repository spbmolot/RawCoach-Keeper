@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' — Блог RawPlan')
@section('description', $post->meta_description ?: $post->getExcerptOrTruncatedBody(160))
@section('keywords', $post->meta_keywords ?: '')

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $post->title }}",
    "description": "{{ $post->getExcerptOrTruncatedBody(160) }}",
    "datePublished": "{{ $post->published_at->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    @if($post->featured_image)
    "image": "{{ Storage::url($post->featured_image) }}",
    @endif
    "author": {
        "@type": "Person",
        "name": "{{ $post->author?->name ?? 'RawPlan' }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "RawPlan",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ url()->current() }}"
    }
}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Навигация --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('blog.index') }}" class="hover:text-green-600 transition">Блог</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if($post->category)
                    <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-green-600 transition">{{ $post->category->name }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @endif
                <span class="text-gray-900 truncate max-w-[200px]">{{ $post->title }}</span>
            </nav>
        </div>
    </div>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        {{-- Header --}}
        <header class="mb-8">
            @if($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}" 
                   class="inline-block text-xs font-semibold text-green-600 uppercase tracking-wider mb-3 hover:text-green-700">
                    {{ $post->category->name }}
                </a>
            @endif

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <p class="text-lg sm:text-xl text-gray-500 leading-relaxed mb-6">{{ $post->excerpt }}</p>
            @endif

            <div class="flex items-center gap-4 text-sm text-gray-400 border-b border-gray-100 pb-6">
                @if($post->author)
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <span class="text-green-600 font-bold text-xs">{{ mb_substr($post->author->name, 0, 1) }}</span>
                        </div>
                        <span class="text-gray-600">{{ $post->author->name }}</span>
                    </div>
                @endif
                <span>{{ $post->published_at->format('d.m.Y') }}</span>
                <span>{{ $post->reading_time }} мин чтения</span>
                <span>{{ number_format($post->views_count) }} просмотров</span>
            </div>
        </header>

        {{-- Featured Image --}}
        @if($post->featured_image)
            <figure class="mb-8 -mx-4 sm:mx-0">
                <img src="{{ Storage::url($post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full rounded-none sm:rounded-2xl object-cover max-h-[500px]">
            </figure>
        @endif

        {{-- Body --}}
        <div class="prose prose-lg prose-green max-w-none mb-12
                    prose-headings:text-gray-900 prose-headings:font-bold
                    prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                    prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3
                    prose-p:text-gray-700 prose-p:leading-relaxed
                    prose-a:text-green-600 prose-a:no-underline hover:prose-a:underline
                    prose-strong:text-gray-900
                    prose-ul:my-4 prose-ol:my-4
                    prose-li:text-gray-700
                    prose-blockquote:border-green-500 prose-blockquote:bg-green-50 prose-blockquote:py-1 prose-blockquote:px-6 prose-blockquote:rounded-r-lg
                    prose-img:rounded-xl">
            {!! $post->body !!}
        </div>

        {{-- Tags --}}
        @if($post->tags && count($post->tags) > 0)
            <div class="flex items-center gap-2 flex-wrap mb-8 border-t border-gray-100 pt-6">
                <span class="text-sm text-gray-500">Теги:</span>
                @foreach($post->tags as $postTag)
                    <a href="{{ route('blog.index', ['tag' => $postTag]) }}" 
                       class="bg-gray-100 hover:bg-green-100 text-gray-600 hover:text-green-700 px-3 py-1 rounded-full text-sm transition">
                        {{ $postTag }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- CTA --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 sm:p-8 text-white text-center mb-12">
            <h3 class="text-xl sm:text-2xl font-bold mb-2">Хотите начать питаться правильно?</h3>
            <p class="text-green-100 mb-4 max-w-lg mx-auto">Попробуйте RawPlan — готовые планы питания на каждый день с рецептами, списками покупок и подсчётом КБЖУ</p>
            <a href="{{ route('plans.index') }}" class="inline-block bg-white text-green-700 font-semibold py-3 px-8 rounded-xl hover:bg-green-50 transition">
                Попробовать бесплатно
            </a>
        </div>

        {{-- Похожие статьи --}}
        @if($relatedPosts->isNotEmpty())
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Читайте также</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $related)
                        <a href="{{ route('blog.show', $related->slug) }}" class="group">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                                @if($related->featured_image)
                                    <img src="{{ Storage::url($related->featured_image) }}" 
                                         alt="{{ $related->title }}" 
                                         class="w-full h-36 object-cover group-hover:scale-105 transition duration-300"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-36 bg-gradient-to-br from-green-100 to-emerald-50 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-green-600 transition line-clamp-2 text-sm">{{ $related->title }}</h3>
                                    <span class="text-xs text-gray-400 mt-2 block">{{ $related->published_at->format('d.m.Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</div>
@endsection
