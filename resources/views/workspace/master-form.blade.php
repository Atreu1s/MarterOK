@extends('layouts.app')
@section('title',$master->exists ? 'Редактирование профиля' : 'Новый мастер')
@section('content')
<section class="container page-intro compact">
<a class="back-link" href="{{ route('workspace.masters') }}">@include('components.icon',['name'=>'back'])К команде</a>
<h1 class="medium-title">{{ $master->exists ? 'Профиль мастера.' : 'Знакомство с мастером.' }}</h1>
</section>
<section class="container section-bottom form-with-aside">
<form class="editor-form" method="post" action="{{ $master->exists ? route('workspace.masters.update',$master) : route('workspace.masters.store') }}" data-submit-form>@csrf @if($master->exists) @method('PUT') @endif
<div class="form-row">
<div class="field">
<label for="name">Имя и фамилия</label>
<input name="name" id="name" value="{{ old('name',$master->name) }}" maxlength="100" required>
</div>
<div class="field">
<label for="slug">Адрес профиля</label>
<input name="slug" id="slug" value="{{ old('slug',$master->slug) }}" maxlength="100" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="ivan-petrov" required>
<span class="field-hint">Латинские буквы, цифры и дефис.</span>
</div>
</div>
<div class="field">
<label for="title">Коротко о подходе</label>
<input name="title" id="title" value="{{ old('title',$master->title) }}" maxlength="160" required>
</div>
<div class="field">
<label for="bio">Описание работы</label>
<textarea name="bio" id="bio" minlength="30" maxlength="4000" rows="5" required>{{ old('bio',$master->bio) }}</textarea>
</div>
<fieldset class="field">
<legend>Направления</legend>
<div class="checkbox-grid">
@foreach(config('repair.services') as $key=>$service)<label class="checkbox-label">
<input type="checkbox" name="services[]" value="{{ $key }}" @checked(in_array($key,old('services',$master->services ?? []),true))>{{ $service['title'] }}</label>@endforeach
</div>
</fieldset>
<div class="form-row">
<div class="field">
<label for="experience_years">Опыт, лет</label>
<input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years',$master->experience_years ?? 1) }}" min="1" max="50" required>
</div>
<div class="field">
<label for="base_price">Начальная цена работы, ₽</label>
<input type="number" name="base_price" id="base_price" value="{{ old('base_price',$master->base_price ?? 500) }}" min="0" max="1000000" required>
</div>
</div>
<div class="form-row">
<div class="field">
<label for="district">Район</label>
<input name="district" id="district" value="{{ old('district',$master->district) }}" maxlength="100" required>
</div>
<div class="field">
<label for="schedule">Время приёма</label>
<input name="schedule" id="schedule" value="{{ old('schedule',$master->schedule) }}" maxlength="160" placeholder="Пн–Пт, 10:00–19:00" required>
</div>
</div>
<input type="hidden" name="is_active" value="0">
<label class="checkbox-label">
<input type="checkbox" name="is_active" value="1" @checked(old('is_active',$master->is_active ?? true))>Профиль принимает новые заявки</label>
<div class="button-row">
<button class="button" data-loading-text="Сохраняем…">Сохранить профиль @include('components.icon',['name'=>'check'])</button>
</div>
</form>
<aside class="form-aside">
<h3>Профиль —<br> это ожидания.</h3>
<p>Опишите специализацию и подход без неподтверждённых обещаний. Укажите время приёма и начальную стоимость работы.</p>
<p>Отключённый профиль не показывается в каталоге. История существующих заявок сохраняется.</p>
</aside>
</section>
@endsection
