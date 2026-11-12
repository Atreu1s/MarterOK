<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Support\ArticleCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function __construct(private ArticleCache $cache) {}

    public function store(CommentRequest $request, Article $article): RedirectResponse
    {
        abort_unless($article->status === 'published', 404);
        $article->comments()->create([
            'body' => $request->validated('body'),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('articles.show', $article)->with('success', 'Комментарий ожидает модерации.');
    }

    /** Список комментариев, которые ещё не прошли модерацию. */
    public function index(): View
    {
        Gate::authorize('moderate');

        $comments = Comment::with(['article', 'user'])->pending()->latest()->paginate(20);

        return view('comments.moderation', [
            'comments' => $comments,
            'pendingCount' => $comments->total(),
        ]);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        Gate::authorize('moderate');
        $comment->approve();
        $this->cache->invalidateDetails($comment->article_id);

        return back()->with('success', 'Комментарий опубликован.');
    }

    public function reject(Comment $comment): RedirectResponse
    {
        Gate::authorize('moderate');
        $comment->delete();
        if ($comment->is_approved) {
            $this->cache->invalidateDetails($comment->article_id);
        }

        return back()->with('success', 'Комментарий отклонён.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        Gate::authorize('moderate');
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:50'],
            'ids.*' => ['integer', 'distinct'],
            'action' => ['required', 'in:approve,reject'],
        ]);

        $selected = Comment::pending()->whereIn('id', $data['ids']);
        if ($data['action'] === 'approve') {
            $articleIds = (clone $selected)->distinct()->pluck('article_id');
            $selected->update(['is_approved' => true]);
            foreach ($articleIds as $articleId) {
                $this->cache->invalidateDetails($articleId);
            }
        } else {
            $selected->delete();
        }

        return back()->with('success', 'Выбранные комментарии обработаны.');
    }
}
