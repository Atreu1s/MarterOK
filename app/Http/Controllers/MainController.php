<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Master;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class MainController extends Controller
{
    /** Лаба 2: данные для главной страницы берутся из public/articles.json. */
    public function index(): View
    {
        return view('home', [
            'entries' => $this->entries(),
            'masters' => Master::where('is_active', true)->orderBy('id')->take(3)->get(),
            'masterCount' => Master::where('is_active', true)->count(),
            'articles' => Article::published()->latest()->take(3)->get(),
        ]);
    }

    /** Страница galery показывает full_image выбранной записи. */
    public function galery(int $index): View
    {
        $entries = $this->entries();
        abort_unless(isset($entries[$index]), 404);

        return view('galery', ['entry' => $entries[$index]]);
    }

    private function entries(): array
    {
        return json_decode(File::get(public_path('articles.json')), true, 512, JSON_THROW_ON_ERROR);
    }
}
