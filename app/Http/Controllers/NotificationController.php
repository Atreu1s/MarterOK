<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Notifications\NewArticleNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $request, string $notification): RedirectResponse
    {
        $entry = $request->user()->notifications()
            ->where('type', NewArticleNotification::class)
            ->findOrFail($notification);
        $entry->markAsRead();

        $article = Article::published()->find($entry->data['article_id']);

        return $article
            ? redirect()->route('articles.show', $article)
            : redirect()->route('articles.index')->with('success', 'Статья больше недоступна.');
    }
}
