<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Mail\Mailable;

class ArticleCreated extends Mailable
{
    public function __construct(public Article $article) {}

    public function build(): static
    {
        return $this->subject('Почин: новая статья — '.$this->article->title)->view('mail.article-created');
    }
}
