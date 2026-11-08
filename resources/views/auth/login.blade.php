@extends('layouts.app')
@section('title', 'Вход')
@section('content')
<section class="container auth-layout">
<div class="auth-story">
<p class="eyebrow">Ваш ремонт — под рукой</p>
<h1>Рады видеть<br>
<em>снова.</em>
</h1>
<p>Заявки, согласование стоимости<br> и история работы — в одном кабинете.</p>
<div class="image-frame">
<img src="{{ asset('images/cooling-detail.webp') }}" alt="Обслуживание системы охлаждения ноутбука" width="1440" height="810">
</div>
</div>
<div class="auth-form">
<span class="small-label">Личный кабинет</span>
<h2>Войти</h2>
<p>Ещё нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a>
</p>
<form method="post" action="{{ route('authenticate') }}" data-submit-form>@csrf<div class="field">
<label for="email">Email</label>
<input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
@error('email')<span class="field-error" id="email-error">{{ $message }}</span>@enderror
</div>
<div class="field">
<label for="password">Пароль</label>
<div class="password-field">
<input id="password" name="password" type="password" autocomplete="current-password" required>
<button class="icon-button" type="button" data-password-toggle="password" aria-label="Показать пароль" aria-pressed="false">@include('components.icon', ['name' => 'eye'])</button>
</div>
@error('password')<span class="field-error">{{ $message }}</span>@enderror
</div>
<button class="button full-width" data-loading-text="Входим…">Войти @include('components.icon', ['name' => 'arrow'])</button>
</form>
<p class="form-footnote">Ваши контакты и фотографии заявок<br> доступны только вам и модератору.</p>
</div>
</section>
@endsection
