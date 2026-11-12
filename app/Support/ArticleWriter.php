<?php

namespace App\Support;

use App\Events\NewArticleEvent;
use App\Http\Requests\ArticleRequest;
use App\Jobs\VeryLongJob;
use App\Models\Article;
use App\Models\User;
use App\Notifications\NewArticleNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ArticleWriter
{
    public function __construct(private ArticleCache $cache) {}

    public function create(ArticleRequest $request): Article
    {
        $data = $this->articleData($request);
        $authorId = $request->user()->id;
        try {
            $article = DB::transaction(function () use ($data, $authorId): Article {
                $article = Article::create($data + ['user_id' => $authorId]);

                if ($article->status === 'published') {
                    User::where('is_blocked', false)
                        ->whereKeyNot($authorId)
                        ->whereHas('role', fn ($query) => $query->where('name', 'reader'))
                        ->chunkById(100, function ($recipients) use ($article): void {
                            Notification::send($recipients, new NewArticleNotification($article));
                        });
                }

                return $article;
            });
        } catch (Throwable $exception) {
            $this->deleteCover($data['cover'] ?? null);
            throw $exception;
        }

        $this->cache->invalidateListings();
        VeryLongJob::dispatch($article->id)->afterCommit();
        if ($article->status === 'published') {
            NewArticleEvent::dispatch($article);
        }

        return $article;
    }

    public function update(ArticleRequest $request, Article $article): void
    {
        $data = $this->articleData($request);
        $previousCover = $article->cover;
        try {
            $article->update($data);
        } catch (Throwable $exception) {
            $this->deleteCover($data['cover'] ?? null);
            throw $exception;
        }

        if (isset($data['cover'])) {
            $this->deleteCover($previousCover);
        }
        $this->cache->invalidateListings();
        $this->cache->invalidateDetails($article->id);
    }

    public function delete(Article $article): void
    {
        $article->delete();
        $this->cache->invalidateListings();
        $this->cache->invalidateDetails($article->id);
        $this->deleteCover($article->cover);
    }

    private function articleData(ArticleRequest $request): array
    {
        $data = $request->safe()->except('cover');
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        return $data;
    }

    private function deleteCover(?string $cover): void
    {
        if ($cover && str_starts_with($cover, 'covers/')) {
            Storage::disk('public')->delete($cover);
        }
    }
}
