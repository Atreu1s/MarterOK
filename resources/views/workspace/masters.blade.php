@extends('layouts.app')
@section('title','Профили мастеров')
@section('content')
<section class="container page-intro compact">
<p class="eyebrow">Рабочее место</p>
<div class="split-heading">
<h1 class="medium-title">Команда.</h1>
<a class="button" href="{{ route('workspace.masters.create') }}">Добавить мастера @include('components.icon',['name'=>'plus'])</a>
</div>
</section>
<section class="container section-bottom">
<div class="team-list">
@forelse($masters as $master)<div class="team-row">
@include('components.master-avatar')
<div>
<h2>{{ $master->name }}</h2>
<p>{{ $master->title }}</p>
<span>{{ $master->district }}</span>
</div>
<span class="status-badge">{{ $master->is_active ? 'Принимает заявки' : 'Скрыт из каталога' }}</span>
<a class="button button-small button-outline" href="{{ route('workspace.masters.edit',$master) }}">Редактировать @include('components.icon',['name'=>'diagonal'])</a>
</div>@empty
<div class="empty-state">
<h2>Начните с первого мастера.</h2>
<p>Добавьте специализацию и условия приёма.</p>
</div>@endforelse
</div>@include('components.pagination',['paginator'=>$masters])</section>
@endsection
