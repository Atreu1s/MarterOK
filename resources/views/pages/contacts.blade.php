@extends('layouts.app')
@section('title', 'Контакты')
@section('content')
<section class="container page-intro">
<p class="eyebrow">Контакты</p>
<h1>Можно просто<br>
<em>спросить.</em>
</h1>
<p class="lead narrow">Если не знаете, какую услугу выбрать, начните с описания неисправности. Номер готовой заявки поможет быстрее найти ваше обращение.</p>
</section>
<section class="container contact-layout section-bottom">
<div class="contact-list">
@foreach($contacts as $label => $value)<div class="contact-row reveal">
<span>{{ $label }}</span>
<strong>{{ $value }}</strong>
</div>@endforeach
<div class="contact-note">Контакты приведены для демонстрации учебного сервиса. Для отправки обращения используйте форму заявки.</div>
<a class="button" href="{{ route('repairs.create') }}">Создать заявку @include('components.icon', ['name' => 'diagonal'])</a>
</div>
<div class="contact-photo image-frame reveal">
<img src="{{ asset('images/workshop.webp') }}" alt="Оборудованное рабочее место мастера" width="1440" height="810">
</div>
</section>
@endsection
