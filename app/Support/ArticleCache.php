<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleCache
{
    public function listing(array $filters, bool $isModerator): array
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $version = Cache::get('articles:list:version', 'initial');
        $key = 'articles:list:'.$version.':'.hash('sha256', serialize([$filters, $isModerator, $page]));

        return Cache::remember($key, now()->addMinutes(10), function () use ($filters, $isModerator, $page): array {
            return [
                'articles' => Article::query()
                    ->when(! $isModerator, fn ($query) => $query->published())
                    ->when($filters['q'] ?? null, fn ($query, $value) => $query->where('title', 'like', '%'.$value.'%'))
                    ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
                    ->when($isModerator && ($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
                    ->latest()
                    ->orderByDesc('id')
                    ->paginate(9, page: $page),
                'categories' => Article::published()->select('category')->distinct()->orderBy('category')->pluck('category'),
                'filters' => $filters,
            ];
        });
    }

    public function details(Article $article): array
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $key = 'articles:detail:'.$article->id;
        $loadComments = fn () => $article->comments()->approved()->with('user')->latest()->orderByDesc('id')->paginate(10, page: $page);

        if ($page === 1) {
            $comments = Cache::lock($key.':lock', 10)->block(5, fn () => Cache::rememberForever($key.':first', $loadComments));
        } else {
            $version = Cache::get($key.':version', 'initial');
            $comments = Cache::remember($key.':'.$version.':'.$page, now()->addMinutes(10), $loadComments);
        }

        $listVersion = Cache::get('articles:list:version', 'initial');
        $related = Cache::remember('articles:related:'.$listVersion.':'.$article->id, now()->addMinutes(10),
            fn () => Article::published()
                ->whereKeyNot($article->id)
                ->where('category', $article->category)
                ->latest()
                ->take(2)
                ->get()
        );

        return compact('article', 'comments', 'related');
    }

    public function invalidateListings(): void
    {
        Cache::forever('articles:list:version', (string) Str::uuid());
    }

    public function invalidateDetails(int $articleId): void
    {
        $key = 'articles:detail:'.$articleId;
        Cache::lock($key.':lock', 10)->block(5, function () use ($key): void {
            Cache::forget($key.':first');
            Cache::forever($key.':version', (string) Str::uuid());
        });
    }
}
