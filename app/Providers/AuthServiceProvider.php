<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\RepairBooking;
use App\Models\User;
use App\Policies\ArticlePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->is_blocked) {
                return false;
            }

            return $user->isModerator() ? true : null;
        });
        Gate::define('moderate', fn (User $user): Response => Response::deny('Это действие доступно только модератору.'));
        Gate::define('view-repair', fn (User $user, RepairBooking $repair): bool => $repair->user_id === $user->id);
    }
}
