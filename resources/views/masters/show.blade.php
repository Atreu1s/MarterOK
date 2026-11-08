@extends('layouts.app')
@section('title', $master->name)
@section('content')
<section class="container page-intro compact">
<a class="back-link" href="{{ route('masters.index') }}">@include('components.icon', ['name' => 'back'])Все мастера</a>
</section>
<section class="container master-profile section-bottom">
<div class="master-profile-main">
<div class="master-profile-header">
@include('components.master-avatar', ['avatarClass' => 'master-avatar-large'])
<div>
<p class="eyebrow">Мастер по ремонту ноутбуков</p>
<h1>{{ $master->name }}</h1>
<p class="lead">{{ $master->title }}</p>
</div>
</div>
<div class="profile-facts">
<div>
<span>Опыт</span>
<strong>{{ $master->experience_years }} лет</strong>
</div>
<div>
<span>Район</span>
<strong>{{ $master->district }}</strong>
</div>
<div>
<span>Приём</span>
<strong>{{ $master->schedule }}</strong>
</div>
</div>
<div class="prose">
<h2>Подход к работе</h2>
<p>{{ $master->bio }}</p>
</div>
<h2 class="subsection-title">С чем поможет</h2>
<div class="service-list">
@foreach($master->services as $key)@php($service = config('repair.services.'.$key))<a href="{{ route('repairs.create', ['master' => $master->id, 'service' => $key]) }}">@include('components.icon', ['name' => $service['icon']])<div>
<h3>{{ $service['title'] }}</h3>
<p>{{ $service['description'] }}</p>
</div>@include('components.icon', ['name' => 'diagonal'])</a>@endforeach
</div>
</div>
<aside class="booking-aside">
<span class="availability">
<i>
</i>Принимает заявки</span>
<p class="price-label">Работа от</p>
<strong class="big-price">{{ number_format($master->base_price, 0, ',', ' ') }} ₽</strong>
<p>Точная стоимость зависит от неисправности и согласуется после диагностики.</p>
<a class="button full-width" href="{{ route('repairs.create', ['master' => $master->id]) }}">Обратиться к мастеру @include('components.icon', ['name' => 'arrow'])</a>
<div class="aside-divider">
</div>
<div class="icon-note">@include('components.icon', ['name' => 'shield'])<span>Ремонт начинается<br> после согласования цены.</span>
</div>
<div class="icon-note">@include('components.icon', ['name' => 'file'])<span>История обращения<br> остаётся в кабинете.</span>
</div>
</aside>
</section>
<section class="container section-bottom profile-examples">@include('components.work-examples', ['examples' => $master->work_examples])</section>
@endsection
