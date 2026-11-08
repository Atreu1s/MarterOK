@extends('layouts.app')
@section('title', 'Заявка №'.$repair->id)
@section('content')
<section class="container page-intro compact">
<a class="back-link" href="{{ route('account') }}">@include('components.icon', ['name' => 'back'])Мои заявки</a>
<p class="eyebrow">Заявка №{{ $repair->id }} · {{ $repair->created_at->format('d.m.Y H:i') }}</p>
<div class="split-heading">
<h1 class="medium-title">{{ $repair->device }}</h1>
<span class="status-badge status-{{ $repair->status }}">{{ $repair->status_label }}</span>
</div>
</section>
<section class="container section-bottom repair-detail">
<div>
<div class="repair-panel">
<div class="panel-heading">
<h2>Ваша задача</h2>
<span>{{ $repair->service_label }}</span>
</div>
<p class="preserve-lines">{{ $repair->description }}</p>
@if($repair->photos)<div class="attachment-grid">
@foreach($repair->photos as $index => $photo)<a href="{{ route('repairs.photo', [$repair, $index]) }}" data-lightbox data-caption="Фотография к заявке №{{ $repair->id }}">
<img src="{{ route('repairs.photo', [$repair, $index]) }}" alt="Приложенная фотография {{ $loop->iteration }}" loading="lazy">@include('components.icon', ['name' => 'diagonal'])</a>@endforeach
</div>@endif
<div class="contact-inline">
<span>{{ $repair->contact_name }}</span>
<span>{{ $repair->phone }}</span>
</div>
</div>
@if($repair->status === 'approval')<section class="quote-panel">
<p class="eyebrow">Нужно ваше решение</p>
<h2>Стоимость ремонта</h2>
<strong class="quote-price">{{ number_format($repair->quoted_price ?? 0, 0, ',', ' ') }} ₽</strong>
@if($repair->public_message)<p>{{ $repair->public_message }}</p>@endif @if($repair->user_id === auth()->id())<form method="post" action="{{ route('repairs.approve', $repair) }}" data-confirm="Подтвердить ремонт за {{ number_format($repair->quoted_price ?? 0, 0, ',', ' ') }} ₽?">@csrf @method('PATCH')<input type="hidden" name="quoted_price" value="{{ $repair->quoted_price }}">
<label class="checkbox-label">
<input type="checkbox" name="consent" value="1" required>
<span>Я согласен с указанной стоимостью ремонта.</span>
</label>
<button class="button">Согласовать ремонт @include('components.icon', ['name' => 'check'])</button>
</form>@else
<p>Согласовать стоимость может владелец заявки в своём кабинете.</p>@endif
</section>@endif
<section class="repair-panel timeline-panel">
<div class="panel-heading">
<h2>История обращения</h2>
<span>Событий: {{ $repair->events->count() }}</span>
</div>
<ol class="timeline">
@foreach($repair->events as $event)<li>
<span class="timeline-dot">
</span>
<div class="timeline-meta">
<strong>{{ App\Models\RepairBooking::STATUSES[$event->status] ?? $event->status }}</strong>
<time>{{ $event->created_at->format('d.m.Y H:i') }}</time>
</div>
<span class="timeline-actor">{{ $event->actor }}</span>
@if($event->message)<p>{{ $event->message }}</p>@endif @if($event->quoted_price)<p class="timeline-price">Стоимость: {{ number_format($event->quoted_price, 0, ',', ' ') }} ₽</p>@endif
</li>@endforeach
</ol>
</section>
</div>
<aside class="booking-aside">
<p class="small-label">Ваш мастер</p>
@if($repair->master)@include('components.master-avatar', ['master' => $repair->master])
<h3>{{ $repair->master->name }}</h3>
<p>{{ $repair->master->title }}</p>
<div class="icon-note">@include('components.icon', ['name' => 'clock'])<span>{{ $repair->master->schedule }}</span>
</div>@else
<h3>Скоро назначим.</h3>
<p>Модератор подберёт специалиста по вашей задаче.</p>@endif
<div class="aside-divider">
</div>
@if($repair->quoted_price)<span class="muted">{{ $repair->quote_accepted_at ? 'Согласованная стоимость' : 'Предложенная стоимость' }}</span>
<strong class="big-price">{{ number_format($repair->quoted_price, 0, ',', ' ') }} ₽</strong>@else
<p>Стоимость появится после диагностики. Ремонт начнётся только после согласования.</p>@endif
<a class="subtle-link" href="{{ route('contacts') }}">Как связаться с сервисом</a>
@if($repair->user_id === auth()->id() && in_array($repair->status, ['new','accepted','diagnosing','approval'], true))<div class="aside-divider">
</div>
<form method="post" action="{{ route('repairs.cancel', $repair) }}" data-confirm="Отменить заявку №{{ $repair->id }}?">@csrf @method('PATCH')<button class="text-button danger-text">Отменить заявку</button>
</form>@endif @if(auth()->user()->isModerator())<div class="aside-divider">
</div>
<a class="plain-link" href="{{ route('workspace.repairs', ['q' => $repair->device]) }}">Управлять заявкой @include('components.icon', ['name' => 'arrow'])</a>@endif
</aside>
</section>
@endsection
