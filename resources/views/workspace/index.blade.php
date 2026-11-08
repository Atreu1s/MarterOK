@extends('layouts.app')
@section('title', 'Рабочее место')
@section('content')
<section class="container page-intro compact">
<p class="eyebrow">Рабочее место</p>
<h1 class="medium-title">Всё важное.<br>
<em>На одном столе.</em>
</h1>
</section>
<section class="container section-bottom">
<div class="metrics-grid">
@foreach([['Новые заявки',$newRepairs,route('workspace.repairs',['status'=>'new'])],['Обращений в работе',$activeRepairs,route('workspace.repairs')],['Комментарии на проверке',$pendingComments,route('comments.moderation')],['Материалов в журнале',$articleCount,route('articles.index')]] as $metric)<a class="metric" href="{{ $metric[2] }}">
<span>{{ $metric[0] }}</span>
<strong>{{ $metric[1] }}</strong>@include('components.icon', ['name'=>'diagonal'])</a>@endforeach
</div>
<div class="section-heading compact-heading">
<h2>Последние обращения</h2>
<a class="plain-link" href="{{ route('workspace.repairs') }}">Все заявки @include('components.icon', ['name'=>'arrow'])</a>
</div>
@forelse($repairs as $repair)<a class="repair-list-card" href="{{ route('repairs.show',$repair) }}">
<div class="repair-list-icon">@include('components.icon', ['name'=>'file'])</div>
<div class="repair-list-main">
<span class="small-label">№{{ $repair->id }} · {{ $repair->contact_name }}</span>
<h3>{{ $repair->device }}</h3>
<p>{{ $repair->service_label }} · {{ $repair->master?->name ?? 'Нужно назначить мастера' }}</p>
</div>
<span class="status-badge status-{{ $repair->status }}">{{ $repair->status_label }}</span>@include('components.icon', ['name'=>'diagonal'])</a>@empty
<div class="empty-state compact-empty">
<h3>Новых обращений пока нет.</h3>
<p>Когда клиент отправит заявку, она появится здесь.</p>
</div>@endforelse
<div class="workspace-shortcuts">
<a href="{{ route('articles.create') }}">
<span>Полезный опыт стоит записать</span>
<h3>Создать материал</h3>@include('components.icon',['name'=>'plus'])</a>
<a href="{{ route('comments.moderation') }}">
<span>Поддержим хороший разговор</span>
<h3>Проверить комментарии</h3>@include('components.icon',['name'=>'message'])</a>
</div>
</section>
@endsection
