@extends('layouts.app')
@section('title', 'О сервисе')
@section('content')
<section class="container page-intro">
<p class="eyebrow">О сервисе</p>
<div class="split-heading">
<h1>Вещам стоит<br> давать <em>шанс.</em>
</h1>
<p>«Почин» соединяет владельца ноутбука с подходящим мастером. Нам важно, чтобы техническая сложность не превращалась в непонятный сервис.</p>
</div>
</section>
<div class="container">
<div class="wide-photo image-frame reveal">
<img src="{{ asset('images/workshop.webp') }}" alt="Рабочее пространство мастерской с инструментами и ноутбуками" width="1440" height="810" fetchpriority="high">
</div>
</div>
<section class="container section about-principles">
<div class="section-heading reveal">
<div>
<p class="eyebrow">Наш подход</p>
<h2>Меньше догадок.<br> Больше ясности.</h2>
</div>
<p class="section-intro">Три правила, вокруг которых<br> устроен сервис.</p>
</div>
<div class="three-columns">
@foreach([['Понимать задачу','Начинаем с симптомов и диагностики. Не предлагаем замену всего устройства только потому, что причина ещё неизвестна.'],['Договариваться заранее','Стоимость и состав работы обсуждаются до ремонта. В личном кабинете отдельно фиксируется ваше согласие.'],['Не терять связь','У заявки есть ответственный мастер и история этапов. Важные изменения остаются в одном месте, а не в случайной переписке.']] as $principle)<article class="principle reveal">
<span class="section-number">0{{ $loop->iteration }}</span>
<h3>{{ $principle[0] }}</h3>
<p>{{ $principle[1] }}</p>
</article>@endforeach
</div>
</section>
<section class="container section">
<div class="editorial-panel">
<div class="editorial-copy">
<p class="eyebrow">Техника и люди</p>
<h2>Сначала человек.<br> Потом <em>отвёртка.</em>
</h2>
<p>У каждого ноутбука есть своя задача: учёба, работа, любимая игра или фотографии. Поэтому хороший ремонт начинается с разговора о том, что важно сохранить.</p>
<a class="button" href="{{ route('masters.index') }}">Познакомиться с мастерами @include('components.icon', ['name' => 'diagonal'])</a>
</div>
<div class="editorial-photo image-frame reveal">
<img src="{{ asset('images/repair-hero.webp') }}" alt="Аккуратная диагностика ноутбука" loading="lazy" width="1440" height="810">
</div>
</div>
</section>
@endsection
