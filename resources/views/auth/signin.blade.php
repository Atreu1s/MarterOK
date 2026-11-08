@extends('layouts.app')
@section('title', 'Регистрация')
@section('content')
<section class="container auth-layout">
<div class="auth-story">
<p class="eyebrow">Знакомство без сложностей</p>
<h1>Хороший ремонт<br> начинается<br> с <em>диалога.</em>
</h1>
<p>Создайте аккаунт, чтобы отправить заявку<br> и не потерять важные детали.</p>
<div class="image-frame">
<img src="{{ asset('images/repair-hero.webp') }}" alt="Работа мастера с ноутбуком" width="1440" height="810">
</div>
</div>
<div class="auth-form">
<span class="small-label">Первый шаг</span>
<h2>Создать аккаунт</h2>
<p>Уже зарегистрированы? <a href="{{ route('login') }}">Войти</a>
</p>
<form method="post" action="{{ route('registration') }}" data-submit-form>@csrf<div class="field">
<label for="name">Как к вам обращаться</label>
<input id="name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="100" required>
@error('name')<span class="field-error">{{ $message }}</span>@enderror
</div>
<div class="field">
<label for="email">Email</label>
<input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required maxlength="255">
@error('email')<span class="field-error">{{ $message }}</span>@enderror
</div>
<div class="field">
<label for="password">Придумайте пароль</label>
<div class="password-field">
<input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required aria-describedby="password-hint">
<button class="icon-button" type="button" data-password-toggle="password" aria-label="Показать пароль" aria-pressed="false">@include('components.icon', ['name' => 'eye'])</button>
</div>
<span class="field-hint" id="password-hint">Не менее 8 символов</span>
@error('password')<span class="field-error">{{ $message }}</span>@enderror
</div>
<div class="field">
<label for="password_confirmation">Повторите пароль</label>
<input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
</div>
<p class="form-footnote align-left">Создавая аккаунт, вы знакомитесь с <a href="{{ route('privacy') }}">правилами использования данных</a>.</p>
<button class="button full-width" data-loading-text="Создаём аккаунт…">Зарегистрироваться @include('components.icon', ['name' => 'arrow'])</button>
</form>
</div>
</section>
@endsection
