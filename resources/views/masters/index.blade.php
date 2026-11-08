@extends('layouts.app')
@section('title', 'Мастера по ремонту ноутбуков')
@section('content')
<section class="container page-intro">
<p class="eyebrow">Мастера · {{ config('site.city') }}</p>
<div class="split-heading">
<h1>Тот, кто<br>
<em>разберётся.</em>
</h1>
<p>Начните со специализации.<br> Посмотрите подход к работе<br> и выберите подходящего человека.</p>
</div>
</section>
<section class="container section-bottom">
<form class="filter-bar" action="{{ route('masters.index') }}" method="get">
<div class="search-field">@include('components.icon', ['name' => 'search'])<label class="sr-only" for="master-search">Имя или район</label>
<input id="master-search" name="q" maxlength="80" placeholder="Имя или район" value="{{ $filters['q'] ?? '' }}">
</div>
<div class="filter-select">
<label class="sr-only" for="master-service">Неисправность</label>
<select id="master-service" name="service">
<option value="">Все направления</option>
@foreach(config('repair.services') as $key => $service)<option value="{{ $key }}" @selected(($filters['service'] ?? '') === $key)>{{ $service['title'] }}</option>@endforeach
</select>
</div>
<div class="filter-select">
<label class="sr-only" for="master-sort">Сортировка</label>
<select id="master-sort" name="sort">
<option value="experience" @selected(($filters['sort'] ?? 'experience') === 'experience')>По опыту</option>
<option value="price" @selected(($filters['sort'] ?? '') === 'price')>По начальной цене</option>
</select>
</div>
<button class="button button-small">Подобрать @include('components.icon', ['name' => 'arrow'])</button>
</form>
<div class="result-caption">
<span>Найдено мастеров: {{ $masters->total() }}</span>
@if(!empty($filters['q']) || !empty($filters['service']))<a class="subtle-link" href="{{ route('masters.index') }}">Сбросить фильтры</a>@else
<span>Стоимость уточняется после диагностики</span>@endif
</div>
<div class="master-grid">
@forelse($masters as $master)@include('components.master-card')@empty
<div class="empty-state full-grid">@include('components.icon', ['name' => 'search'])<h2>Подберём другой вариант.</h2>
<p>По выбранным условиям мастеров не нашлось. Измените фильтры или отправьте заявку без конкретного специалиста.</p>
<div class="button-row">
<a class="button button-outline" href="{{ route('masters.index') }}">Все мастера</a>
<a class="button" href="{{ route('repairs.create') }}">Описать задачу @include('components.icon', ['name' => 'arrow'])</a>
</div>
</div>@endforelse
</div>@include('components.pagination', ['paginator' => $masters])<div class="inline-help">
<div>@include('components.icon', ['name' => 'message'])<h3>Не знаете, кого выбрать?</h3>
<p>Опишите поломку — назначим мастера подходящего направления.</p>
</div>
<a class="plain-link" href="{{ route('repairs.create') }}">Начать с задачи @include('components.icon', ['name' => 'diagonal'])</a>
</div>
</section>
@endsection
