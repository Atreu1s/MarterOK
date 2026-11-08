@extends('layouts.app')
@section('title', 'Заявка на ремонт')
@section('content')
<section class="container page-intro compact">
<p class="eyebrow">Заявка на ремонт</p>
<div class="split-heading">
<h1>Расскажите,<br>
<em>что случилось.</em>
</h1>
<p>Нужны модель и ваши наблюдения.<br> Технические термины знать не обязательно.</p>
</div>
</section>
<section class="container section-bottom wizard-layout">
<form method="post" enctype="multipart/form-data" action="{{ route('repairs.store') }}" class="repair-form" data-wizard data-submit-form data-error-fields="{{ json_encode($errors->keys()) }}">@csrf<input type="hidden" name="submission_key" value="{{ old('submission_key', $submissionKey) }}">
<nav class="wizard-nav js-only" aria-label="Шаги заявки">
<button type="button" data-step-go="0" aria-current="step">
<span>01</span>Неисправность</button>
<button type="button" data-step-go="1">
<span>02</span>Мастер</button>
<button type="button" data-step-go="2">
<span>03</span>Контакты</button>
<div class="wizard-progress">
<i>
</i>
</div>
</nav>
<section class="wizard-step" data-step="0">
<div class="step-heading">
<span class="section-number">01</span>
<div>
<h2>Что нужно проверить?</h2>
<p>Выберите наиболее похожий симптом.</p>
</div>
</div>
<fieldset class="repair-services">
<legend class="sr-only">Тип неисправности</legend>
@foreach(config('repair.services') as $key => $service)<label class="repair-service">
<input type="radio" name="service" value="{{ $key }}" @checked(old('service', $selected['service'] ?? 'diagnostics') === $key) data-service-label="{{ $service['title'] }}" required>
<span>@include('components.icon', ['name' => $service['icon']])<strong>{{ $service['name'] }}</strong>
<i>@include('components.icon', ['name' => 'check'])</i>
</span>
</label>@endforeach
</fieldset>
<div class="field">
<label for="device">Марка и модель ноутбука</label>
<input id="device" name="device" value="{{ old('device') }}" maxlength="120" placeholder="Например, ThinkPad T14 или модель с наклейки" required autocomplete="off">
<span class="field-hint">Если точную модель не знаете, укажите то, что известно.</span>
</div>
<div class="field">
<label for="description">Что происходит</label>
<textarea id="description" name="description" rows="5" minlength="15" maxlength="4000" placeholder="Когда появилась проблема? Горят ли индикаторы? Что меняется при подключении зарядки?" required data-character-count>{{ old('description') }}</textarea>
<div class="field-hint field-counter">
<span>От 15 символов. Не добавляйте пароли и личные документы.</span>
<span data-count-for="description">{{ mb_strlen(old('description', '')) }}/4000</span>
</div>
</div>
<button class="button js-only" type="button" data-next-step>Выбрать мастера @include('components.icon', ['name' => 'arrow'])</button>
</section>
<section class="wizard-step" data-step="1">
<div class="step-heading">
<span class="section-number">02</span>
<div>
<h2>Кому доверим задачу?</h2>
<p>Можно выбрать специалиста или оставить назначение нам.</p>
</div>
</div>
<fieldset class="master-options">
<legend class="sr-only">Мастер</legend>
<label class="master-option automatic-option">
<input type="radio" name="master_id" value="" @checked((string)old('master_id', $selected['master'] ?? '') === '') data-master-label="Назначит модератор">
<span>
<span class="master-avatar">@include('components.icon', ['name' => 'wrench'])</span>
<span>
<strong>Подберите мне мастера</strong>
<small>Назначим специалиста по вашей задаче</small>
</span>
<i>@include('components.icon', ['name' => 'check'])</i>
</span>
</label>
@foreach($masters as $master)<label class="master-option" data-master-option data-services="{{ json_encode($master->services) }}">
<input type="radio" name="master_id" value="{{ $master->id }}" @checked((string)old('master_id', $selected['master'] ?? '') === (string)$master->id) data-master-label="{{ $master->name }}">
<span>
@include('components.master-avatar')
<span>
<strong>{{ $master->name }}</strong>
<small>{{ $master->title }}</small>
<small>{{ $master->district }} · {{ $master->experience_years }} лет опыта</small>
</span>
<i>@include('components.icon', ['name' => 'check'])</i>
</span>
</label>@endforeach
</fieldset>
<p class="field-hint js-only" data-master-filter-note>Показаны мастера выбранного направления.</p>
<div class="button-row js-only">
<button class="button button-outline" type="button" data-prev-step>@include('components.icon', ['name' => 'back'])Назад</button>
<button class="button" type="button" data-next-step>Перейти к контактам @include('components.icon', ['name' => 'arrow'])</button>
</div>
</section>
<section class="wizard-step" data-step="2">
<div class="step-heading">
<span class="section-number">03</span>
<div>
<h2>Как с вами связаться?</h2>
<p>Контакты увидят только вы и модератор.</p>
</div>
</div>
<div class="form-row">
<div class="field">
<label for="contact_name">Ваше имя</label>
<input id="contact_name" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" autocomplete="name" maxlength="100" required>
</div>
<div class="field">
<label for="phone">Телефон с кодом страны</label>
<input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" placeholder="+7 999 000-00-00" autocomplete="tel" maxlength="25" required>
</div>
</div>
<div class="field">
<label for="photos">Фотографии, если помогут</label>
<label class="upload-zone" for="photos">@include('components.icon', ['name' => 'upload'])<span>
<strong>Выберите до трёх фотографий</strong>
<small>JPG, PNG, WebP · каждая до 4 МБ</small>
</span>
<input type="file" name="photos[]" id="photos" accept="image/jpeg,image/png,image/webp" multiple data-photo-input>
</label>
<div data-photo-preview class="photo-previews">
</div>
<p class="field-error" data-photo-error role="status">
</p>
<span class="field-hint">Не фотографируйте открытые документы и переписку.</span>
</div>
<div class="request-review js-only">
<h3>Проверим перед отправкой</h3>
<dl>
<div>
<dt>Устройство</dt>
<dd data-review-device>—</dd>
</div>
<div>
<dt>Направление</dt>
<dd data-review-service>Диагностика</dd>
</div>
<div>
<dt>Мастер</dt>
<dd data-review-master>Назначит модератор</dd>
</div>
</dl>
</div>
<label class="checkbox-label consent-label">
<input type="checkbox" name="consent" value="1" @checked(old('consent')) required>
<span>Согласен на обработку данных заявки и ознакомился с <a href="{{ route('privacy') }}">правилами конфиденциальности</a>.</span>
</label>
<div class="button-row">
<button class="button button-outline js-only" type="button" data-prev-step>@include('components.icon', ['name' => 'back'])Назад</button>
<button class="button" data-loading-text="Отправляем заявку…">Отправить заявку @include('components.icon', ['name' => 'diagonal'])</button>
</div>
</section>
</form>
<aside class="form-aside request-aside">
<span class="aside-icon">@include('components.icon', ['name' => 'wrench'])</span>
<h3>Вы описываете.<br> Мастер разбирается.</h3>
<p>Никаких обязательств по ремонту до диагностики и согласования цены.</p>
<ol class="aside-steps">
<li>Заявка сохранится в кабинете</li>
<li>Модератор уточнит детали</li>
<li>Мастер предложит план и стоимость</li>
<li>Вы подтвердите решение</li>
</ol>
<a class="subtle-link" href="{{ route('home') }}#how">Подробнее об этапах</a>
</aside>
</section>
@endsection
