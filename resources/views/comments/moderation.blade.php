@extends('layouts.app')
@section('title', 'Модерация комментариев')
@section('content')
<section class="container page-intro compact">
<p class="eyebrow">Модерация</p>
<div class="split-heading">
<h1 class="medium-title">Полезный разговор<br> начинается здесь.</h1>
<p>{{ $pendingCount }} комментариев ожидают решения.<br> Публикуйте то, что помогает обсуждению.</p>
</div>
</section>
<section class="container section-bottom">
@if($comments->isNotEmpty())<form id="bulk-comments" method="post" action="{{ route('comments.bulk') }}" data-confirm="Применить выбранное решение ко всем отмеченным комментариям?">@csrf<div class="bulk-toolbar">
<label class="checkbox-label">
<input type="checkbox" data-select-all>Выбрать все на странице</label>
<div>
<select name="action" aria-label="Действие с выбранными комментариями">
<option value="approve">Принять выбранные</option>
<option value="reject">Отклонить выбранные</option>
</select>
<button class="button button-small" data-bulk-submit>Применить @include('components.icon', ['name' => 'check'])</button>
</div>
</div>
</form>
<div class="moderation-list">
@foreach($comments as $comment)<article class="moderation-card">
<div class="moderation-selector">
<input type="checkbox" name="ids[]" value="{{ $comment->id }}" form="bulk-comments" data-bulk-item aria-label="Выбрать комментарий {{ $comment->id }}">
</div>
<div>
<div class="comment-head">
<strong>{{ $comment->user->name }}</strong>
<time>{{ $comment->created_at->format('d.m.Y H:i') }}</time>
</div>
<p class="moderation-body">{{ $comment->body }}</p>
<a class="subtle-link" href="{{ route('articles.show', $comment->article) }}">{{ $comment->article->title }}</a>
</div>
<div class="moderation-actions">
<form method="post" action="{{ route('comments.approve', $comment) }}">@csrf @method('PATCH')<button class="button button-small">Принять @include('components.icon', ['name' => 'check'])</button>
</form>
<form method="post" action="{{ route('comments.reject', $comment) }}" data-confirm="Отклонить этот комментарий?">@csrf @method('PATCH')<button class="button button-small button-outline">Отклонить @include('components.icon', ['name' => 'close'])</button>
</form>
</div>
</article>@endforeach
</div>@include('components.pagination', ['paginator' => $comments])@else
<div class="empty-state">@include('components.icon', ['name' => 'check'])<h2>Всё разобрано.</h2>
<p>Новых комментариев на проверку пока нет.</p>
<a class="button button-outline" href="{{ route('workspace') }}">В рабочее место @include('components.icon', ['name' => 'arrow'])</a>
</div>@endif
</section>
@endsection
