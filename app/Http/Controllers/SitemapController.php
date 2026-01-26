<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Recipe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = Cache::remember('sitemap_xml', 3600, function () {
            $menus = Menu::where('is_published', true)->get();
            $recipes = Recipe::where('is_published', true)->get();
            
            return view('sitemap.index', compact('menus', 'recipes'))->render();
        });

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
