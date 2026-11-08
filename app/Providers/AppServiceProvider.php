<?php

namespace App\Providers;

use App\Models\Comment;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        ViewFacade::composer('layouts.app', function (View $view): void {
            $count = auth()->user()?->isModerator() ? Comment::pending()->count() : 0;
            $view->with('moderationCount', $count);
        });
        Password::defaults(fn () => Password::min(8));
    }
}
