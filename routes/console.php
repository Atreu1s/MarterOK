<?php

use Illuminate\Support\Facades\Schedule;

if (config('site.article_statistics_every_minute')) {
    Schedule::command('pochin:article-statistics --today')->everyMinute()->withoutOverlapping();
} else {
    Schedule::command('pochin:article-statistics')->daily()->withoutOverlapping();
}
