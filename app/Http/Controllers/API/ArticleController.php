<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Comment;
use App\Support\ArticleCache;
use App\Support\ArticleWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    public function __construct(private ArticleCache $cache, private ArticleWriter $writer) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $this->cache->validateFilters($request);
        $isModerator = Auth::guard('sanctum')->user()?->isModerator() ?? false;
        $data = $this->cache->listing($filters, $isModerator);
        $articles = (clone $data['articles'])->withPath($request->url())->withQueryString();

        return ArticleResource::collection($articles)->additional([
            'categories' => $data['categories'],
            'filters' => $data['filters'],
        ]);
    }

    public function show(Request $request, Article $article): ArticleResource
    {
        abort_unless($article->status === 'published' || Auth::guard('sanctum')->user()?->isModerator(), 404);

        $data = $this->cache->details($article);
        $comments = (clone $data['comments'])->withPath($request->url());
        $comments->setCollection($comments->getCollection()->map(fn (Comment $comment): array => [
            'id' => $comment->id,
            'body' => $comment->body,
            'author' => $comment->user?->name,
            'created_at' => $comment->created_at,
        ]));

        return (new ArticleResource($article))->additional([
            'comments' => $comments,
            'related' => ArticleResource::collection($data['related']),
        ]);
    }

    public function store(ArticleRequest $request): JsonResponse
    {
        $article = $this->writer->create($request);

        return (new ArticleResource($article))->response()->setStatusCode(201);
    }

    public function update(ArticleRequest $request, Article $article): ArticleResource
    {
        $this->writer->update($request, $article);

        return new ArticleResource($article);
    }

    public function destroy(Article $article): Response
    {
        Gate::authorize('delete', $article);
        $this->writer->delete($article);

        return response()->noContent();
    }
}
