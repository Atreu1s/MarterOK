<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Support\ArticleCache;
use App\Support\ArticleWriter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    public function __construct(private ArticleCache $cache, private ArticleWriter $writer) {}

    public function index(Request $request): View
    {
        $filters = $this->cache->validateFilters($request);
        $isModerator = $request->user()?->isModerator() ?? false;
        $data = $this->cache->listing($filters, $isModerator);
        $data['articles'] = (clone $data['articles'])->withPath($request->url())->withQueryString();

        return view('articles.index', $data);
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === 'published' || auth()->user()?->isModerator(), 404);

        $data = $this->cache->details($article);
        $data['comments'] = (clone $data['comments'])->withPath(request()->url());

        return view('articles.show', $data);
    }

    public function create(): View
    {
        Gate::authorize('create', Article::class);

        return view('articles.form', ['article' => new Article]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $article = $this->writer->create($request);

        return redirect()->route('articles.show', $article)->with('success', 'Материал создан.');
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('articles.form', compact('article'));
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $this->writer->update($request, $article);

        return redirect()->route('articles.show', $article)->with('success', 'Изменения сохранены.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);
        $this->writer->delete($article);

        return redirect()->route('articles.index')->with('success', 'Материал удалён.');
    }
}
