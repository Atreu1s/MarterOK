@extends('layouts.app')
@section('title', $entry['title'])
@section('content')
<section class="container page-intro compact">
<a class="back-link" href="{{ route('home') }}#find">@include('components.icon', ['name' => 'back'])На главную</a>
<h1 class="medium-title">{{ $entry['title'] }}</h1>
<p class="lead">{{ $entry['description'] }}</p>
</section>
<figure class="container section-bottom gallery-detail">
<a href="{{ asset($entry['full_image']) }}" data-lightbox data-caption="{{ $entry['title'] }}">
<img src="{{ asset($entry['full_image']) }}" alt="{{ $entry['title'] }}" width="1440" height="810">
<span>Открыть крупнее @include('components.icon', ['name' => 'diagonal'])</span>
</a>
<figcaption>{{ $entry['description'] }}</figcaption>
</figure>
@endsection
