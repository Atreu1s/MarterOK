<?php

namespace App\Console\Commands;

use App\Models\ArticleView;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendArticleStatistics extends Command
{
    protected $signature = 'pochin:article-statistics {--today : Сформировать отчёт за текущий день}';

    protected $description = 'Отправить модераторам статистику просмотров и новых комментариев';

    public function handle(): int
    {
        $start = now()->startOfDay();
        if (! $this->option('today')) {
            $start->subDay();
        }
        $end = $start->copy()->addDay();
        $date = $start->format('d.m.Y');
        $views = ArticleView::where('created_at', '>=', $start)->where('created_at', '<', $end)->count();
        $comments = Comment::where('created_at', '>=', $start)->where('created_at', '<', $end)->count();
        $body = "Статистика за {$date}\nПросмотры статей: {$views}\nНовые комментарии: {$comments}";
        $failed = false;

        $moderators = User::where('is_blocked', false)
            ->whereHas('role', fn ($query) => $query->where('name', 'moderator'))
            ->lazyById();
        foreach ($moderators as $moderator) {
            try {
                Mail::raw($body, fn ($message) => $message->to($moderator->email)->subject("Почин: статистика за {$date}"));
            } catch (Throwable $exception) {
                report($exception);
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
