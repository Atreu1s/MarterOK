<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Jobs\VeryLongJob;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'in:draft,published'],
        ]);
        $isModerator = $request->user()?->isModerator() ?? false;

        $articles = Article::query()
            ->when(! $isModerator, fn ($query) => $query->published())
            ->when($filters['q'] ?? null, fn ($query, $value) => $query->where('title', 'like', '%'.$value.'%'))
            ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
            ->when($isModerator && ($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $categories = Article::published()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('articles.index', compact('articles', 'categories', 'filters'));
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === 'published' || auth()->user()?->isModerator(), 404);

        // Лаба 9: на странице статьи показываем только одобренные комментарии.
        $comments = $article->comments()->approved()->with('user')->latest()->paginate(10);
        $related = Article::published()
            ->whereKeyNot($article->id)
            ->where('category', $article->category)
            ->latest()
            ->take(2)
            ->get();

        return view('articles.show', compact('article', 'comments', 'related'));
    }

    public function create(): View
    {
        Gate::authorize('create', Article::class);

        return view('articles.form', ['article' => new Article]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        Gate::authorize('create', Article::class);
        $data = $request->safe()->except('cover');
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }
        try {
            $article = Article::create($data + ['user_id' => $request->user()->id]);
        } catch (\Throwable $exception) {
            if (isset($data['cover'])) {
                Storage::disk('public')->delete($data['cover']);
            }
            throw $exception;
        }

        VeryLongJob::dispatch($article->id)->afterCommit();

        return redirect()->route('articles.show', $article)->with('success', 'Материал создан.');
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('articles.form', compact('article'));
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        Gate::authorize('update', $article);
        $data = $request->safe()->except('cover');
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }
        $previous = $article->cover;
        try {
            $article->update($data);
        } catch (\Throwable $exception) {
            if (isset($data['cover'])) {
                Storage::disk('public')->delete($data['cover']);
            }
            throw $exception;
        }
        if (isset($data['cover']) && $previous && str_starts_with($previous, 'covers/')) {
            Storage::disk('public')->delete($previous);
        }

        return redirect()->route('articles.show', $article)->with('success', 'Изменения сохранены.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);
        $cover = $article->cover;
        $article->delete();
        if ($cover && str_starts_with($cover, 'covers/')) {
            Storage::disk('public')->delete($cover);
        }

        return redirect()->route('articles.index')->with('success', 'Материал удалён.');
    }

}
