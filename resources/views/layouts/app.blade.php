<!DOCTYPE html>
<html lang="ru" id="top">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ff4f00">
    <meta name="description" content="Почин — выбор мастера по ремонту ноутбуков. Опишите неисправность, согласуйте стоимость и следите за ремонтом в личном кабинете.">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Ремонт начинается с понятного решения') — Почин</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="@yield('body-class')">
    <a class="skip-link" href="#main">Перейти к содержимому</a>
    <svg class="icon-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
<defs>
<symbol id="icon-arrow" viewBox="0 0 24 24">
<path d="M4 12h15M13 5l7 7-7 7"/>
</symbol>
<symbol id="icon-diagonal" viewBox="0 0 24 24">
<path d="M5 19 19 5M5 5h14v14"/>
</symbol>
<symbol id="icon-chevron" viewBox="0 0 24 24">
<path d="m8 4 8 8-8 8"/>
</symbol>
<symbol id="icon-close" viewBox="0 0 24 24">
<path d="m5 5 14 14M19 5 5 19"/>
</symbol>
<symbol id="icon-check" viewBox="0 0 24 24">
<path d="m5 12 4 4 10-11"/>
</symbol>
<symbol id="icon-menu" viewBox="0 0 24 24">
<path d="M3 6h18M3 12h18M3 18h18"/>
</symbol>
<symbol id="icon-user" viewBox="0 0 24 24">
<circle cx="12" cy="8" r="4"/>
<path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
</symbol>
<symbol id="icon-power" viewBox="0 0 24 24">
<path d="M12 2v10M6 5a9 9 0 1 0 12 0"/>
</symbol>
<symbol id="icon-fan" viewBox="0 0 24 24">
<circle cx="12" cy="12" r="2"/>
<path d="M10 10C4 8 5 2 10 3c3 0 4 3 2 7M14 10c2-6 8-5 7 0 0 3-3 4-7 2M14 14c6 2 5 8 0 7-3 0-4-3-2-7M10 14c-2 6-8 5-7 0 0-3 3-4 7-2"/>
</symbol>
<symbol id="icon-battery" viewBox="0 0 24 24">
<rect x="2" y="6" width="18" height="12" rx="2"/>
<path d="M22 10v4M7 9v6M11 9v6M15 9v6"/>
</symbol>
<symbol id="icon-keyboard" viewBox="0 0 24 24">
<rect x="2" y="5" width="20" height="14" rx="2"/>
<path d="M6 9h1m3 0h1m3 0h1m3 0h1M6 12h1m3 0h1m3 0h1m3 0h1M7 15h10"/>
</symbol>
<symbol id="icon-screen" viewBox="0 0 24 24">
<rect x="2" y="3" width="20" height="15" rx="2"/>
<path d="M8 22h8M12 18v4m-4-8 4-6 4 6"/>
</symbol>
<symbol id="icon-chip" viewBox="0 0 24 24">
<rect x="6" y="6" width="12" height="12" rx="2"/>
<path d="M9 1v5m6-5v5M9 18v5m6-5v5M1 9h5m-5 6h5m12-6h5m-5 6h5M10 10h4v4h-4z"/>
</symbol>
<symbol id="icon-clock" viewBox="0 0 24 24">
<circle cx="12" cy="12" r="9"/>
<path d="M12 6v6l4 2"/>
</symbol>
<symbol id="icon-pin" viewBox="0 0 24 24">
<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/>
<circle cx="12" cy="10" r="2.5"/>
</symbol>
<symbol id="icon-shield" viewBox="0 0 24 24">
<path d="m12 2 8 4v6c0 5-8 10-8 10S4 17 4 12V6l8-4Z"/>
<path d="m8 12 3 3 5-6"/>
</symbol>
<symbol id="icon-wrench" viewBox="0 0 24 24">
<path d="M14 6a5 5 0 0 0-6 6L2 18a3 3 0 0 0 4 4l6-6a5 5 0 0 0 6-6l-4 3-3-3 3-4Z"/>
</symbol>
<symbol id="icon-mail" viewBox="0 0 24 24">
<rect x="2" y="4" width="20" height="16" rx="2"/>
<path d="m2 5 10 8L22 5"/>
</symbol>
<symbol id="icon-phone" viewBox="0 0 24 24">
<path d="m5 3 4 4-2 3c2 4 3 5 7 7l3-2 4 4c-1 3-3 4-6 3C8 19 5 16 2 9 1 6 2 4 5 3Z"/>
</symbol>
<symbol id="icon-search" viewBox="0 0 24 24">
<circle cx="10" cy="10" r="7"/>
<path d="m16 16 6 6"/>
</symbol>
<symbol id="icon-upload" viewBox="0 0 24 24">
<path d="M12 16V2m-5 5 5-5 5 5M3 15v6h18v-6"/>
</symbol>
<symbol id="icon-file" viewBox="0 0 24 24">
<path d="M5 2h9l5 5v15H5V2Z"/>
<path d="M14 2v6h5M8 12h8m-8 4h6"/>
</symbol>
<symbol id="icon-logout" viewBox="0 0 24 24">
<path d="M9 3H3v18h6M8 12h13m-5-5 5 5-5 5"/>
</symbol>
<symbol id="icon-eye" viewBox="0 0 24 24">
<path d="M2 12S6 5 12 5s10 7 10 7-4 7-10 7S2 12 2 12Z"/>
<circle cx="12" cy="12" r="3"/>
</symbol>
<symbol id="icon-plus" viewBox="0 0 24 24">
<path d="M12 3v18M3 12h18"/>
</symbol>
<symbol id="icon-back" viewBox="0 0 24 24">
<path d="M20 12H5m6-7-7 7 7 7"/>
</symbol>
<symbol id="icon-message" viewBox="0 0 24 24">
<path d="M21 3H3v14h5v5l6-5h7V3Z"/>
<path d="M7 8h10M7 12h7"/>
</symbol>
<symbol id="icon-heart" viewBox="0 0 24 24">
<path d="M20 5a5 5 0 0 0-8 1 5 5 0 0 0-8-1c-4 5 2 11 8 16 6-5 12-11 8-16Z"/>
</symbol>
</defs>
</svg>
    <div class="reading-progress" data-reading-progress>
</div>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="Почин — на главную">почин<span class="brand-mark" aria-hidden="true">
<i>
</i>
<i>
</i>
<i>
</i>
<i>
</i>
</span>
</a>
            <nav class="main-nav" id="main-nav" aria-label="Основная навигация">
                <a href="{{ route('masters.index') }}" @if(request()->routeIs('masters.*')) aria-current="page" @endif>Мастера</a>
                <a href="{{ route('home') }}#how">Как это работает</a>
                <a href="{{ route('articles.index') }}" @if(request()->routeIs('articles.*')) aria-current="page" @endif>Журнал</a>
                <a href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>О нас</a>
                <a class="mobile-contact" href="{{ route('contacts') }}">Контакты</a>
            </nav>
            <div class="header-actions">
                @auth
                    <a class="account-link" href="{{ route('account') }}">@include('components.icon', ['name' => 'user'])<span>Кабинет</span>
</a>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="icon-button logout-button" aria-label="Выйти из аккаунта" title="Выйти">@include('components.icon', ['name' => 'logout'])</button>
</form>
                @else
                    <a class="account-link" href="{{ route('login') }}">@include('components.icon', ['name' => 'user'])<span>Войти</span>
</a>
                @endauth
                <a class="button button-small header-request" href="{{ route('repairs.create') }}">Описать поломку @include('components.icon', ['name' => 'diagonal'])</a>
                <button type="button" class="icon-button menu-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="Открыть меню" data-menu-toggle>@include('components.icon', ['name' => 'menu'])</button>
            </div>
        </div>
        @auth @if(auth()->user()->isModerator())
            <nav class="workspace-nav" aria-label="Навигация модератора">
<div class="container">
                <a href="{{ route('workspace') }}" @if(request()->routeIs('workspace')) aria-current="page" @endif>Рабочее место</a>
                <a href="{{ route('workspace.repairs') }}" @if(request()->routeIs('workspace.repairs*')) aria-current="page" @endif>Заявки</a>
                <a href="{{ route('comments.moderation') }}" @if(request()->routeIs('comments.*')) aria-current="page" @endif>Комментарии <span class="count-pill">{{ $moderationCount ?? 0 }}</span>
</a>
                <a href="{{ route('articles.create') }}">Создать материал</a>
                <a href="{{ route('workspace.masters') }}" @if(request()->routeIs('workspace.masters*')) aria-current="page" @endif>Мастера</a>
            </div>
</nav>
        @endif @endauth
    </header>
    <main id="main" tabindex="-1">
        @if(session('success'))<div class="container">
<div class="notice notice-success" role="status">@include('components.icon', ['name' => 'check'])<span>{{ session('success') }}</span>
<button type="button" class="icon-button" data-dismiss aria-label="Закрыть сообщение">@include('components.icon', ['name' => 'close'])</button>
</div>
</div>@endif
        @if($errors->any())<div class="container">
<div class="notice notice-error" role="alert" data-error-summary tabindex="-1">
<div>
<strong>Проверьте заполнение формы</strong>
<ul>
@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
</ul>
</div>
</div>
</div>@endif
        @yield('content')
    </main>
    <footer class="site-footer">
        <div class="container footer-top">
<div>
<a class="brand" href="{{ route('home') }}">почин<span class="brand-dot">
</span>
</a>
<p>Не всё, что сломалось,<br> нужно менять на новое.</p>
</div>
<div class="footer-links">
<a href="{{ route('masters.index') }}">Выбрать мастера</a>
<a href="{{ route('articles.index') }}">Журнал о технике</a>
<a href="{{ route('about') }}">О сервисе</a>
<a href="{{ route('contacts') }}">Контакты</a>
</div>
<div class="footer-action">
<p>Начнём с вашей задачи?</p>
<a class="button button-light" href="{{ route('repairs.create') }}">Описать поломку @include('components.icon', ['name' => 'diagonal'])</a>
<span>{{ config('site.hours') }}</span>
</div>
</div>
        <div class="container footer-bottom">
<span>© {{ date('Y') }} Почин. Учебный сервис.<br> Профили, адрес и цены — демонстрационные.</span>
<div>
<a href="{{ route('privacy') }}">Конфиденциальность</a>
<button type="button" class="text-button js-only" data-cookie-open>Настройки cookies</button>
<button type="button" class="text-button js-only" data-motion-toggle aria-pressed="false">Уменьшить анимации</button>
</div>
<span>{{ config('site.author') }}<br> Группа {{ config('site.group') }}</span>
</div>
    </footer>
    <a href="#top" class="back-top" data-back-top aria-label="Наверх">@include('components.icon', ['name' => 'arrow', 'class' => 'rotate-up'])</a>
    <section class="consent-panel" data-cookie-banner aria-label="Настройки cookies" hidden>
<span class="small-label">Ваши настройки</span>
<h2>Запомним, как вам удобно?</h2>
<p>Обязательные cookies поддерживают вход. По желанию сохраним выбор неисправности и режим анимации на этом устройстве.</p>
<div class="consent-buttons">
<button class="button button-small" type="button" data-cookie-accept>Разрешить</button>
<button class="button button-small button-outline" type="button" data-cookie-necessary>Только необходимые</button>
</div>
<button type="button" class="text-button" data-cookie-open>Настроить подробнее</button>
</section>
    <dialog class="modal" id="cookie-dialog" aria-labelledby="cookie-title">
<div class="modal-header">
<h2 id="cookie-title">Настройки cookies</h2>
<button class="icon-button" type="button" data-close-dialog aria-label="Закрыть">@include('components.icon', ['name' => 'close'])</button>
</div>
<p>Вы можете изменить решение в любой момент через ссылку внизу страницы.</p>
<label class="switch-row">
<span>
<strong>Обязательные</strong>
<small>Вход, безопасность форм и сообщения сайта.</small>
</span>
<input type="checkbox" checked disabled aria-label="Обязательные cookies всегда включены">
</label>
<label class="switch-row">
<span>
<strong>Предпочтения</strong>
<small>Выбор неисправности и уменьшение анимации.</small>
</span>
<input type="checkbox" data-cookie-preferences>
</label>
<button class="button full-width" type="button" data-cookie-save>Сохранить выбор @include('components.icon', ['name' => 'check'])</button>
<a class="subtle-link" href="{{ route('privacy') }}">Как используются данные</a>
</dialog>
    <dialog class="modal" id="confirm-dialog" aria-labelledby="confirm-title">
<div class="modal-header">
<h2 id="confirm-title">Подтвердите действие</h2>
<button class="icon-button" type="button" data-close-dialog aria-label="Закрыть">@include('components.icon', ['name' => 'close'])</button>
</div>
<p data-confirm-message>
</p>
<div class="button-row">
<button class="button button-outline" type="button" data-close-dialog>Вернуться</button>
<button class="button" type="button" data-confirm-accept>Подтвердить @include('components.icon', ['name' => 'arrow'])</button>
</div>
</dialog>
    <dialog class="lightbox" id="image-dialog" aria-labelledby="image-caption">
<button class="icon-button" type="button" data-close-dialog aria-label="Закрыть изображение">@include('components.icon', ['name' => 'close'])</button>
<img alt="" data-lightbox-image>
<p id="image-caption">
</p>
</dialog>
    <div class="toast" data-toast role="status" hidden>
</div>
</body>
</html>
