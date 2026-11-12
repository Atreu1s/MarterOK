<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class NewArticleEvent implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;

    private array $article;

    public function __construct(Article $article)
    {
        $this->article = [
            'id' => $article->id,
            'title' => $article->title,
            'url' => route('articles.show', $article),
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('articles');
    }

    public function broadcastWith(): array
    {
        return ['article' => $this->article];
    }
}
