@extends('layouts.app')
@section('title', $article->exists ? 'Редактирование материала' : 'Новый материал')
@section('content')
<section class="container page-intro compact">
<a class="back-link" href="{{ route('articles.index') }}">@include('components.icon', ['name' => 'back'])В журнал</a>
<p class="eyebrow">Редактор</p>
<h1 class="medium-title">{{ $article->exists ? 'Уточним детали.' : 'Есть чем поделиться?' }}</h1>
</section>
<section class="container section-bottom form-with-aside">
<form method="post" enctype="multipart/form-data" action="{{ $article->exists ? route('articles.update', $article) : route('articles.store') }}" class="editor-form" data-submit-form>@csrf @if($article->exists) @method('PUT') @endif
<div class="field">
<label for="title">Заголовок</label>
<input id="title" name="title" value="{{ old('title', $article->title) }}" maxlength="255" required>
</div>
<div class="field">
<label for="excerpt">Краткое описание</label>
<textarea id="excerpt" name="excerpt" rows="3" maxlength="500" required>{{ old('excerpt', $article->excerpt) }}</textarea>
<span class="field-hint">Помогает понять, о чём материал, ещё до открытия.</span>
</div>
<div class="field">
<label for="body">Текст</label>
<textarea id="body" name="body" rows="15" maxlength="50000" required>{{ old('body', $article->body) }}</textarea>
<span class="field-hint">Обычный текст. Абзацы и переносы сохранятся при публикации.</span>
</div>
<div class="form-row">
<div class="field">
<label for="category">Тема</label>
<input id="category" name="category" list="article-categories" value="{{ old('category', $article->category) }}" maxlength="80" required>
<datalist id="article-categories">
<option value="Ремонт">
<option value="Уход">
<option value="Как это работает">
<option value="Выбор техники">
</datalist>
</div>
<div class="field">
<label for="status">Состояние</label>
<select id="status" name="status">
<option value="draft" @selected(old('status', $article->status ?? 'draft') === 'draft')>Черновик</option>
<option value="published" @selected(old('status', $article->status) === 'published')>Опубликовано</option>
</select>
</div>
</div>
<div class="field">
<label for="cover">Обложка</label>
<input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" data-cover-input>
<span class="field-hint">JPG, PNG или WebP, не более 3 МБ. Новая загрузка заменит прежнюю обложку.</span>
<img class="cover-preview" data-cover-preview src="{{ $article->cover_url }}" alt="Предварительный просмотр обложки">
</div>
<div class="button-row">
<button class="button" data-loading-text="Сохраняем…">{{ $article->exists ? 'Сохранить изменения' : 'Создать материал' }} @include('components.icon', ['name' => 'check'])</button>
<a class="subtle-link" href="{{ route('articles.index') }}">Вернуться без сохранения</a>
</div>
</form>
<aside class="form-aside">
<span class="section-number">Журнал</span>
<h3>Понятный текст<br> лучше сложного.</h3>
<p>Опишите одну задачу, объясните последовательность и оставьте читателю полезный вывод.</p>
<div class="aside-divider">
</div>
<p>Черновик виден модератору. Для читателей материал появится после выбора состояния «Опубликовано».</p>
<p>После создания нового материала активным модераторам отправляется уведомление.</p>
</aside>
</section>
@endsection
