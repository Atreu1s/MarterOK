@extends('layouts.app')
@section('title', 'Управление заявками')
@section('content')
<section class="container page-intro compact">
<p class="eyebrow">Рабочее место</p>
<h1 class="medium-title">Заявки<br>
<em>и следующие шаги.</em>
</h1>
</section>
<section class="container section-bottom">
<form class="filter-bar" method="get" action="{{ route('workspace.repairs') }}">
<div class="search-field">@include('components.icon',['name'=>'search'])<label class="sr-only" for="repair-search">Устройство или имя клиента</label>
<input id="repair-search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Устройство или имя клиента">
</div>
<select name="status" aria-label="Этап заявки">
<option value="">Все этапы</option>
@foreach(App\Models\RepairBooking::STATUSES as $key=>$label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach
</select>
<button class="button button-small">Найти @include('components.icon',['name'=>'arrow'])</button>
</form>
<div class="result-caption">{{ $repairs->total() }} обращений</div>
<div class="work-items">
@forelse($repairs as $repair)@php($isOld = (string)old('_repair_id') === (string)$repair->id)<details class="work-item" @if($isOld || $loop->first) open @endif>
<summary>
<span class="work-number">№{{ $repair->id }}</span>
<div>
<strong>{{ $repair->device }}</strong>
<small>{{ $repair->contact_name }} · {{ $repair->created_at->format('d.m.Y H:i') }}</small>
</div>
<span class="status-badge status-{{ $repair->status }}">{{ $repair->status_label }}</span>@include('components.icon',['name'=>'plus'])</summary>
<div class="work-item-body">
<div class="work-context">
<h3>{{ $repair->service_label }}</h3>
<p class="preserve-lines">{{ $repair->description }}</p>
<p class="contact-inline">
<span>{{ $repair->contact_name }}</span>
<span>{{ $repair->phone }}</span>
</p>
<a class="plain-link" href="{{ route('repairs.show',$repair) }}">Заявка, фото и история @include('components.icon',['name'=>'diagonal'])</a>
@if($repair->quote_accepted_at)<p class="field-hint">Клиент согласовал {{ number_format($repair->quoted_price,0,',',' ') }} ₽ · {{ $repair->quote_accepted_at->format('d.m.Y H:i') }}</p>@endif
</div>
<form method="post" action="{{ route('workspace.repairs.update',$repair) }}" data-submit-form>@csrf @method('PATCH')<input type="hidden" name="_repair_id" value="{{ $repair->id }}">
<div class="form-row">
<div class="field">
<label for="status-{{ $repair->id }}">Этап</label>
<select name="status" id="status-{{ $repair->id }}">
@foreach(array_unique(array_merge([$repair->status],App\Models\RepairBooking::TRANSITIONS[$repair->status])) as $status)@if($status !== 'repairing' || $repair->quote_accepted_at)<option value="{{ $status }}" @selected(($isOld ? old('status') : $repair->status) === $status)>{{ App\Models\RepairBooking::STATUSES[$status] }}</option>@endif @endforeach
</select>
</div>
<div class="field">
<label for="master-{{ $repair->id }}">Ответственный мастер</label>
<select name="master_id" id="master-{{ $repair->id }}">
<option value="">Не назначен</option>
@foreach($masters->filter(fn($master)=>in_array($repair->service,$master->services,true)) as $master)<option value="{{ $master->id }}" @selected((string)($isOld ? old('master_id') : $repair->master_id) === (string)$master->id)>{{ $master->name }}</option>@endforeach
</select>
</div>
</div>
<div class="field">
<label for="price-{{ $repair->id }}">Стоимость для согласования, ₽</label>
<input type="number" name="quoted_price" id="price-{{ $repair->id }}" min="1" max="1000000" value="{{ $isOld ? old('quoted_price') : $repair->quoted_price }}" @if(in_array($repair->status,['repairing','ready','closed','cancelled'],true)) readonly @endif>
<span class="field-hint">Переход к ремонту выполнит клиент после подтверждения суммы.</span>
</div>
<div class="field">
<label for="public-{{ $repair->id }}">Сообщение клиенту</label>
<textarea name="public_message" id="public-{{ $repair->id }}" maxlength="2000" rows="3">{{ $isOld ? old('public_message') : $repair->public_message }}</textarea>
</div>
<div class="field">
<label for="internal-{{ $repair->id }}">Внутренняя заметка</label>
<textarea name="internal_note" id="internal-{{ $repair->id }}" maxlength="4000" rows="2">{{ $isOld ? old('internal_note') : $repair->internal_note }}</textarea>
<span class="field-hint">Клиент не видит эту заметку.</span>
</div>
<button class="button button-small" data-loading-text="Сохраняем…">Сохранить изменения @include('components.icon',['name'=>'check'])</button>
</form>
</div>
</details>@empty
<div class="empty-state">
<h2>Всё спокойно.</h2>
<p>По этим условиям заявок нет.</p>
<a class="plain-link" href="{{ route('workspace.repairs') }}">Все обращения @include('components.icon',['name'=>'arrow'])</a>
</div>@endforelse
</div>@include('components.pagination',['paginator'=>$repairs])</section>
@endsection
