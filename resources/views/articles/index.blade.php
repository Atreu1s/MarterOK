@extends('layouts.app')
@section('title', 'Журнал о ремонте и уходе за техникой')
@section('content')
<section class="container page-intro">
<p class="eyebrow">Журнал «Почин»</p>
<div class="split-heading">
<h1>Понимать<br>
<em>свою технику.</em>
</h1>
<p>Материалы о ремонте, уходе и решениях,<br> которые помогают не покупать лишнее.</p>
</div>
</section>
<section class="container section-bottom">
<form class="filter-bar" method="get" action="{{ route('articles.index') }}">
<div class="search-field">@include('components.icon', ['name' => 'search'])<label class="sr-only" for="journal-search">Поиск по заголовку</label>
<input id="journal-search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Найти материал">
</div>
<div class="filter-select">
<label class="sr-only" for="category">Тема</label>
<select name="category" id="category">
<option value="">Все темы</option>
@foreach($categories as $category)<option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>@endforeach
</select>
</div>
@auth @if(auth()->user()->isModerator())<div class="filter-select">
<label class="sr-only" for="journal-status">Публикация</label>
<select name="status" id="journal-status">
<option value="">Все состояния</option>
<option value="published" @selected(($filters['status'] ?? '') === 'published')>Опубликовано</option>
<option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Черновики</option>
</select>
</div>@endif @endauth
<button class="button button-small">Найти @include('components.icon', ['name' => 'arrow'])</button>
@if(!empty($filters['q']) || !empty($filters['category']) || !empty($filters['status']))<a class="subtle-link" href="{{ route('articles.index') }}">Сбросить</a>@endif
</form>
<div class="result-caption">
<span>{{ $articles->total() }} материалов</span>
@auth @if(auth()->user()->isModerator())<a class="plain-link" href="{{ route('articles.create') }}">Создать материал @include('components.icon', ['name' => 'plus'])</a>@endif @endauth
</div>
<div class="article-grid">
@forelse($articles as $article)@include('components.article-card')@empty
<div class="empty-state full-grid">@include('components.icon', ['name' => 'search'])<h2>Такой материал пока не нашёлся</h2>
<p>Попробуйте другое слово или выберите все темы.</p>
<a class="button button-outline" href="{{ route('articles.index') }}">Все материалы @include('components.icon', ['name' => 'arrow'])</a>
</div>@endforelse
</div>@include('components.pagination', ['paginator' => $articles])</section>
@endsection
