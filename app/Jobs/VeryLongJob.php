<?php

namespace App\Jobs;

use App\Mail\ArticleCreated;
use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class VeryLongJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $articleId) {}

    public function handle(): void
    {
        $article = Article::find($this->articleId);
        if (! $article) {
            return;
        }

        $moderators = User::where('is_blocked', false)
            ->whereHas('role', fn ($query) => $query->where('name', 'moderator'))
            ->get();

        foreach ($moderators as $moderator) {
            try {
                Mail::to($moderator)->send(new ArticleCreated($article));
            } catch (Throwable $exception) {
                Log::warning('Письмо о статье не отправлено.', [
                    'article_id' => $article->id,
                    'moderator_id' => $moderator->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
