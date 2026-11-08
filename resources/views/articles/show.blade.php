@extends('layouts.app')
@section('title', $article->title)
@section('content')
<article data-reading>
<header class="container article-header">
<a class="back-link" href="{{ route('articles.index') }}">@include('components.icon', ['name' => 'back'])В журнал</a>
<div class="article-card-meta">
<span>{{ $article->category }}</span>
<span>{{ $article->reading_minutes }} мин чтения</span>
<time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('d.m.Y') }}</time>
@if($article->status === 'draft')<span class="status-badge">Черновик</span>@endif
</div>
<h1>{{ $article->title }}</h1>
<p class="lead">{{ $article->excerpt }}</p>
@auth @if(auth()->user()->isModerator())<div class="button-row editor-actions">
<a class="button button-small button-outline" href="{{ route('articles.edit', $article) }}">Редактировать @include('components.icon', ['name' => 'file'])</a>
<form method="post" action="{{ route('articles.destroy', $article) }}" data-confirm="Удалить этот материал вместе с его комментариями?">@csrf @method('DELETE')<button class="text-button danger-text">Удалить материал</button>
</form>
</div>@endif @endauth
</header>
<figure class="container article-main-image image-frame">
<img src="{{ $article->cover_url }}" alt="Иллюстрация к материалу: {{ $article->title }}" width="1440" height="810">
</figure>
<div class="container article-content-layout">
<aside class="article-side">
<span class="small-label">Время разобраться</span>
<p>Полезный материал<br> из журнала «Почин».</p>
<a href="{{ route('repairs.create') }}">Нужна помощь с техникой? @include('components.icon', ['name' => 'diagonal'])</a>
</aside>
<div class="article-copy">
<div class="prose">{!! nl2br(e($article->body)) !!}</div>
<div class="article-end">
<span>Остался вопрос о неисправности?</span>
<a class="plain-link" href="{{ route('masters.index') }}">Выберите мастера @include('components.icon', ['name' => 'arrow'])</a>
</div>
</div>
</div>
</article>
<section class="container comments-section" id="comments">
<div class="comments-heading">
<h2>Обсудим?</h2>
<p>Комментарии появляются после проверки.</p>
</div>
<div class="comments-layout">
<div>
@forelse($comments as $comment)<article class="comment">
<div class="comment-head">
<span class="comment-avatar" aria-hidden="true">{{ mb_substr($comment->user->name, 0, 1) }}</span>
<strong>{{ $comment->user->name }}</strong>
<time datetime="{{ $comment->created_at->toDateString() }}">{{ $comment->created_at->format('d.m.Y') }}</time>
</div>
<p>{{ $comment->body }}</p>
</article>@empty
<div class="empty-comments">@include('components.icon', ['name' => 'message'])<h3>Здесь можно начать разговор</h3>
<p>Поделитесь наблюдением или задайте вопрос по материалу.</p>
</div>@endforelse @include('components.pagination', ['paginator' => $comments])</div>
<div class="comment-form">
@auth @if($article->status === 'published')<form method="post" action="{{ route('comments.store', $article) }}" data-submit-form>@csrf<h3>Ваш комментарий</h3>
<div class="field">
<label class="sr-only" for="comment-body">Текст комментария</label>
<textarea id="comment-body" name="body" minlength="3" maxlength="3000" rows="5" placeholder="Что думаете об этом?" required>{{ old('body') }}</textarea>
<div class="field-hint">От 3 до 3000 символов. Не указывайте личные данные.</div>
</div>
<button class="button full-width" data-loading-text="Отправляем…">Отправить @include('components.icon', ['name' => 'arrow'])</button>
</form>@else
<p>Обсуждение откроется после публикации.</p>@endif @else
<h3>Присоединяйтесь к обсуждению</h3>
<p>Для комментария понадобится аккаунт. Это помогает поддерживать спокойный и полезный разговор.</p>
<a class="button button-outline" href="{{ route('login') }}">Войти @include('components.icon', ['name' => 'arrow'])</a>@endauth
</div>
</div>
</section>
@if($related->isNotEmpty())<section class="section container">
<div class="section-heading">
<h2>Ещё по теме.</h2>
<a class="plain-link" href="{{ route('articles.index') }}">В журнал @include('components.icon', ['name' => 'arrow'])</a>
</div>
<div class="article-grid article-grid-two">
@foreach($related as $article)@include('components.article-card')@endforeach
</div>
</section>@endif
@endsection
