<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>@yield('code') — Почин</title>
<link rel="icon" href="{{ asset('favicon.svg') }}">@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body>
<main class="container error-page">
<a class="brand" href="{{ url('/') }}">почин<span class="brand-dot">
</span>
</a>
<p class="eyebrow">@yield('code')</p>
<h1>@yield('heading')</h1>
<p>@yield('message')</p>
<div class="button-row">
<a href="{{ url('/') }}" class="button">На главную</a>
<a href="{{ url('/account') }}" class="button button-outline">В кабинет</a>
</div>
</main>
</body>
</html>
