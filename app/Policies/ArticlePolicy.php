<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    public function view(?User $user, Article $article): bool
    {
        return $article->status === 'published';
    }

    public function create(User $user): Response
    {
        return Response::deny('Публиковать материалы может только модератор.');
    }

    public function update(User $user, Article $article): Response
    {
        return Response::deny('Редактировать материалы может только модератор.');
    }

    public function delete(User $user, Article $article): Response
    {
        return Response::deny('Удалять материалы может только модератор.');
    }
}
