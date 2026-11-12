<?php

namespace App\Http\Middleware;

use App\Models\Article;
use App\Models\ArticleView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordArticleView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $article = $request->route('article');

        if ($request->isMethod('GET') && $response->isSuccessful() && $article instanceof Article && $article->status === 'published') {
            ArticleView::create(['article_id' => $article->id, 'url' => $request->url()]);
        }

        return $response;
    }
}
