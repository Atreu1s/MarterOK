@extends('layouts.app')
@section('title', 'Дайте ноутбуку второй шанс')
@section('body-class', 'home-page')
@section('content')
<section class="hero container">
    <div class="hero-copy">
<p class="eyebrow">
<span class="signal-dot">
</span>Ремонт ноутбуков · {{ config('site.city') }}</p>
<h1>
<span class="hero-line">Не спешите</span>
<span class="hero-line">покупать</span>
<span class="hero-line">
<em class="draw-underline">новый.</em>
</span>
</h1>
<p class="hero-description">Хорошую технику стоит сохранить.<br> Найдите мастера, согласуйте стоимость<br class="desktop-break"> и следите за ремонтом в одном месте.</p>
<div class="hero-buttons">
<a class="button" href="{{ route('masters.index') }}">Найти своего мастера @include('components.icon', ['name' => 'diagonal'])</a>
<a class="plain-link" href="#how">Как всё устроено @include('components.icon', ['name' => 'arrow'])</a>
</div>
<div class="hero-note">@include('components.icon', ['name' => 'shield'])<span>Сначала диагностика.<br> Ремонт — после вашего согласия.</span>
</div>
</div>
    <div class="hero-visual">
<div class="hero-photo image-frame">
<img src="{{ asset('images/repair-hero.webp') }}" alt="Мастер проверяет открытый ноутбук на рабочем столе" width="1440" height="810" fetchpriority="high">
<div class="hero-photo-label">
<span class="signal-dot">
</span>Внимание к деталям</div>
</div>
<div class="hero-caption">
<span>Техника сложная.<br>
<strong>Сервис должен быть понятным.</strong>
</span>
<a class="round-link" href="{{ route('about') }}" aria-label="Узнать о сервисе">@include('components.icon', ['name' => 'diagonal'])</a>
</div>

</div>
</section>
<section class="fault-section container reveal" id="find">
<div class="fault-heading">
<span class="section-number">01</span>
<h2>Что случилось<br> с ноутбуком?</h2>
<p>Начнём с симптома.<br> Диагноз поставит мастер.</p>
</div>
<form method="get" action="{{ route('masters.index') }}" data-fault-picker>
<fieldset class="fault-grid">
<legend class="sr-only">Выберите неисправность</legend>
@foreach(config('repair.services') as $key => $service)<label class="fault-option">
<input type="radio" name="service" value="{{ $key }}" @checked($loop->first) data-title="{{ $service['title'] }}" data-price="{{ $service['price'] }}">
<span>@include('components.icon', ['name' => $service['icon']])<span>{{ $service['name'] }}</span>
<span class="fault-check">@include('components.icon', ['name' => 'check'])</span>
</span>
</label>@endforeach
</fieldset>
<div class="fault-result">
<p>
<span class="muted">Направление</span>
<strong data-fault-title>Диагностика</strong>
</p>
<p class="fault-price">Работа от <strong data-fault-price>500 ₽</strong>
<small>Точная стоимость — после проверки</small>
</p>
<button class="button" type="submit">Подобрать мастера @include('components.icon', ['name' => 'arrow'])</button>
</div>
</form>
</section>
<section class="section container" id="how">
<div class="section-heading reveal">
<div>
<p class="eyebrow">Без непонятных этапов</p>
<h2>От поломки<br> до «всё работает».</h2>
</div>
<p class="section-intro">Один понятный путь.<br> Все изменения — в вашем кабинете.</p>
</div>
<div class="process-grid" data-process>
<div class="process-track">
<span data-process-progress>
</span>
</div>
@foreach([['Опишите задачу','Модель, симптомы и пара фотографий. Можно выбрать мастера или оставить назначение нам.'],['Передайте на диагностику','Мастер уточнит детали и проверит устройство. До этого окончательная цена не обещается.'],['Согласуйте стоимость','Предложение появится в кабинете. Только вы решаете, приступать ли к ремонту.'],['Заберите рабочий ноутбук','Следите за историей заявки. Когда работа завершена, статус сообщит о готовности.']] as $step)<article class="process-step reveal">
<span class="process-number">0{{ $loop->iteration }}</span>
<h3>{{ $step[0] }}</h3>
<p>{{ $step[1] }}</p>
</article>@endforeach
</div>
</section>
<section class="masters-section section">
<div class="container">
<div class="section-heading reveal">
<div>
<p class="eyebrow">Люди, которые разбираются</p>
<h2>У каждой задачи —<br> свой мастер.</h2>
</div>
<div>
<p class="section-intro">Выбирайте по специализации,<br> району и подходу к работе.</p>
<a class="plain-link" href="{{ route('masters.index') }}">Все мастера <span class="count-inline">{{ $masterCount }}</span> @include('components.icon', ['name' => 'arrow'])</a>
</div>
</div>
<div class="master-grid">
@forelse($masters as $master)@include('components.master-card')@empty
<div class="empty-state">
<h3>Список мастеров обновляется</h3>
<p>Вы можете отправить заявку без выбранного специалиста.</p>
<a class="plain-link" href="{{ route('repairs.create') }}">Описать поломку @include('components.icon', ['name' => 'arrow'])</a>
</div>@endforelse
</div>
</div>
</section>
<section class="section container">
<div class="editorial-panel reveal">
<div class="editorial-photo image-frame">
<img src="{{ asset('images/workshop.webp') }}" alt="Светлая мастерская с оборудованием для диагностики ноутбуков" loading="lazy" width="1440" height="810">
</div>
<div class="editorial-copy">
<p class="eyebrow">Здравый подход к ремонту</p>
<h2>Не меняем<br> то, что можно<br>
<em>починить.</em>
</h2>
<p>Мы за понятные решения. Сначала разобраться в неисправности, затем объяснить варианты и договориться о работе.</p>
<a class="plain-link" href="{{ route('about') }}">Ближе познакомиться @include('components.icon', ['name' => 'diagonal'])</a>
</div>
</div>
</section>
<section class="section container">
<div class="section-heading reveal">
<div>
<p class="eyebrow">Журнал</p>
<h2>Разбираться<br> не обязательно.<br>
<span class="muted">Понимать — полезно.</span>
</h2>
</div>
<a class="plain-link" href="{{ route('articles.index') }}">Все материалы @include('components.icon', ['name' => 'arrow'])</a>
</div>
<div class="article-grid">
@forelse($articles as $article)@include('components.article-card')@empty
<p class="empty-copy">Здесь появятся материалы об уходе за техникой.</p>@endforelse
</div>
</section>
<section class="section container gallery-section">
<div class="section-heading reveal">
<div>
<p class="eyebrow">За рабочим столом</p>
<h2>Ремонт —<br> это внимание.</h2>
</div>
<p class="section-intro">Посмотрите на работу<br> и пространство чуть ближе.</p>
</div>
<div class="gallery-grid">
@foreach($entries as $index => $entry)<a class="gallery-card reveal" href="{{ route('galery', $index) }}">
<div class="image-frame">
<img src="{{ asset($entry['preview_image']) }}" alt="{{ $entry['title'] }}" loading="lazy" width="640" height="360">
<span class="image-arrow">@include('components.icon', ['name' => 'diagonal'])</span>
</div>
<div>
<span>0{{ $loop->iteration }}</span>
<h3>{{ $entry['title'] }}</h3>
</div>
</a>@endforeach
</div>
</section>
<section class="section container">@include('components.work-examples', ['examples' => config('repair.examples')])</section>
<section class="section container faq-section">
<div class="section-heading reveal">
<div>
<p class="eyebrow">До первой заявки</p>
<h2>Хорошие<br> вопросы.</h2>
</div>
<a class="plain-link" href="{{ route('contacts') }}">Остались другие? @include('components.icon', ['name' => 'diagonal'])</a>
</div>
<div class="faq-list reveal">
@foreach([['Можно отправить заявку без выбранного мастера?','Да. Опишите модель и неисправность, а модератор назначит специалиста подходящего направления. В кабинете будет видно, кто ведёт вашу заявку.'],['Цена в каталоге — окончательная?','Нет. Это начальный ориентир стоимости работы. После диагностики мастер предложит конкретную сумму, а вы подтвердите её в личном кабинете.'],['Кто увидит прикреплённые фотографии?','Фотографии доступны владельцу заявки и модератору. Они не публикуются в журнале или каталоге мастеров.'],['Как отменить обращение?','В кабинете можно отменить заявку до начала ремонта. Если работа уже идёт, свяжитесь с мастерской, чтобы обсудить дальнейшие действия.']] as $faq)<details>
<summary>{{ $faq[0] }}<span>@include('components.icon', ['name' => 'plus'])</span>
</summary>
<p>{{ $faq[1] }}</p>
</details>@endforeach
</div>
</section>
<section class="closing-cta">
<div class="container">
<p class="eyebrow">Уже знаете, что случилось?</p>
<h2>Давайте<br> разберёмся.</h2>
<a class="button button-light" href="{{ route('repairs.create') }}">Описать поломку @include('components.icon', ['name' => 'diagonal'])</a>
</div>
</section>
@endsection
