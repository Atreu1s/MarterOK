@extends('layouts.app')
@section('title', 'Личный кабинет')
@section('content')
<section class="container page-intro">
<p class="eyebrow">Личный кабинет</p>
<div class="split-heading">
<h1>Здравствуйте,<br>
<em>{{ explode(' ', auth()->user()->name)[0] }}.</em>
</h1>
<div>
<p>Здесь ваши обращения<br> и понятная история работы.</p>
<a class="button button-small" href="{{ route('repairs.create') }}">Новая заявка @include('components.icon', ['name' => 'plus'])</a>
</div>
<form method="post" action="{{ route('logout') }}" data-submit-form>@csrf<button class="text-button">Выйти из аккаунта</button>
</form>
</div>
</section>
<section class="container section-bottom">
<div class="account-heading">
<div>
<h2>Мои заявки <span class="count-inline">{{ $repairs->total() }}</span>
</h2>
<p>{{ $activeCount }} в работе</p>
</div>
<form method="get" action="{{ route('account') }}" class="inline-filter">
<label class="sr-only" for="account-status">Этап заявки</label>
<select name="status" id="account-status">
<option value="">Все этапы</option>
@foreach(App\Models\RepairBooking::STATUSES as $key => $label)<option value="{{ $key }}" @selected($filter === $key)>{{ $label }}</option>@endforeach
</select>
<button class="button button-small button-outline">Показать</button>
</form>
</div>
@forelse($repairs as $repair)<a class="repair-list-card reveal" href="{{ route('repairs.show', $repair) }}">
<div class="repair-list-icon">@include('components.icon', ['name' => config('repair.services.'.$repair->service.'.icon')])</div>
<div class="repair-list-main">
<span class="small-label">Заявка №{{ $repair->id }} · {{ $repair->created_at->format('d.m.Y') }}</span>
<h3>{{ $repair->device }}</h3>
<p>{{ $repair->service_label }} · {{ $repair->master?->name ?? 'Мастер ещё не назначен' }}</p>
</div>
<div class="repair-list-status">
<span class="status-badge status-{{ $repair->status }}">{{ $repair->status_label }}</span>
@if($repair->quoted_price)<strong>{{ number_format($repair->quoted_price, 0, ',', ' ') }} ₽</strong>@endif
</div>@include('components.icon', ['name' => 'diagonal'])</a>@empty
<div class="empty-state">
<div class="empty-device">@include('components.icon', ['name' => 'screen'])<span>@include('components.icon', ['name' => 'check'])</span>
</div>
<h2>{{ $filter ? 'На этом этапе заявок нет.' : 'Пусть техника не подводит.' }}</h2>
<p>{{ $filter ? 'Выберите другой этап или посмотрите все обращения.' : 'А если что-то случится — опишите неисправность. Здесь появятся заявка, предложение мастера и история ремонта.' }}</p>
<a class="button" href="{{ $filter ? route('account') : route('repairs.create') }}">{{ $filter ? 'Все заявки' : 'Описать поломку' }} @include('components.icon', ['name' => 'arrow'])</a>
</div>@endforelse @include('components.pagination', ['paginator' => $repairs])<div class="account-info">
<span>{{ auth()->user()->email }}</span>
<span>Роль: {{ auth()->user()->role?->label ?? 'Читатель' }}</span>
@if(auth()->user()->isModerator())<a class="plain-link" href="{{ route('workspace') }}">Рабочее место @include('components.icon', ['name' => 'arrow'])</a>@endif
</div>
</section>
@endsection
