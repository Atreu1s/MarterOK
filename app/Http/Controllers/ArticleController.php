<?php

namespace App\Http\Controllers;

use App\Events\NewArticleEvent;
use App\Http\Requests\ArticleRequest;
use App\Jobs\VeryLongJob;
use App\Models\Article;
use App\Models\User;
use App\Notifications\NewArticleNotification;
use App\Support\ArticleCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function __construct(private ArticleCache $cache) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'in:draft,published'],
        ]);
        $isModerator = $request->user()?->isModerator() ?? false;

        $data = $this->cache->listing($filters, $isModerator);
        $data['articles']->withPath($request->url())->withQueryString();

        return view('articles.index', $data);
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === 'published' || auth()->user()?->isModerator(), 404);

        $data = $this->cache->details($article);
        $data['comments']->withPath(request()->url());

        return view('articles.show', $data);
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

        $this->cache->invalidateListings();
        VeryLongJob::dispatch($article->id)->afterCommit();
        if ($article->status === 'published') {
            NewArticleEvent::dispatch($article);
            $readers = User::where('is_blocked', false)
                ->whereKeyNot($request->user()->id)
                ->whereHas('role', fn ($query) => $query->where('name', 'reader'));

            $readers->chunkById(100, function ($recipients) use ($article): void {
                Notification::send($recipients, new NewArticleNotification($article));
            });
        }

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
        $this->cache->invalidateListings();
        $this->cache->invalidateDetails($article->id);

        return redirect()->route('articles.show', $article)->with('success', 'Изменения сохранены.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);
        $cover = $article->cover;
        $article->delete();
        $this->cache->invalidateListings();
        $this->cache->invalidateDetails($article->id);
        if ($cover && str_starts_with($cover, 'covers/')) {
            Storage::disk('public')->delete($cover);
        }

        return redirect()->route('articles.index')->with('success', 'Материал удалён.');
    }
}
