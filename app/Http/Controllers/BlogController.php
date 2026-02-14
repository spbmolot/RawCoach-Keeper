<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /**
     * Список статей блога
     */
    public function index(Request $request)
    {
        $query = BlogPost::published()
            ->with(['category', 'author'])
            ->orderByDesc('published_at');

        // Фильтр по тегу
        if ($tag = $request->get('tag')) {
            $query->withTag($tag);
        }

        // Поиск
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(9);

        $categories = Cache::remember('blog_categories', 3600, function () {
            return BlogCategory::active()
                ->ordered()
                ->withCount(['posts' => fn($q) => $q->published()])
                ->get();
        });

        // Популярные статьи для сайдбара
        $popularPosts = Cache::remember('blog_popular_posts', 1800, function () {
            return BlogPost::published()
                ->popular()
                ->limit(5)
                ->get(['id', 'title', 'slug', 'views_count', 'published_at']);
        });

        // Все теги
        $allTags = Cache::remember('blog_all_tags', 3600, function () {
            return BlogPost::published()
                ->whereNotNull('tags')
                ->pluck('tags')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take(20);
        });

        return view('blog.index', compact('posts', 'categories', 'popularPosts', 'allTags', 'search', 'tag'));
    }

    /**
     * Просмотр статьи
     */
    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->firstOrFail();

        // Увеличиваем просмотры (не чаще раза в сессию)
        $sessionKey = 'blog_viewed_' . $post->id;
        if (!session()->has($sessionKey)) {
            $post->incrementViews();
            session()->put($sessionKey, true);
        }

        $relatedPosts = $post->relatedPosts(3);

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Статьи категории
     */
    public function category(string $slug)
    {
        $category = BlogCategory::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = BlogPost::published()
            ->where('category_id', $category->id)
            ->with(['author'])
            ->orderByDesc('published_at')
            ->paginate(9);

        $categories = Cache::remember('blog_categories', 3600, function () {
            return BlogCategory::active()
                ->ordered()
                ->withCount(['posts' => fn($q) => $q->published()])
                ->get();
        });

        return view('blog.category', compact('category', 'posts', 'categories'));
    }
}
