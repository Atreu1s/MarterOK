<article class="master-card reveal">
    @if($master->avatar_url)
    <a class="master-card-photo image-frame" href="{{ route('masters.show', $master) }}" aria-label="Профиль: {{ $master->name }}">
        <img src="{{ $master->avatar_url }}" alt="{{ $master->name }}" width="720" height="720" loading="lazy">
        <span class="portrait-availability"><i></i>Принимает заявки</span>
    </a>
    @else
    <div class="master-card-top">@include('components.master-avatar')<span class="availability"><i></i>Принимает заявки</span></div>
    @endif
    <p class="master-experience">{{ $master->experience_years }} {{ $master->experience_years % 10 === 1 && $master->experience_years % 100 !== 11 ? 'год' : (in_array($master->experience_years % 10, [2,3,4]) && !in_array($master->experience_years % 100, [12,13,14]) ? 'года' : 'лет') }} опыта</p>
    <h3>
<a href="{{ route('masters.show', $master) }}">{{ $master->name }}</a>
</h3>
<p class="master-title">{{ $master->title }}</p>
    <div class="tags">
@foreach(array_slice($master->services, 0, 3) as $service)<span>{{ config('repair.services.'.$service.'.title') }}</span>@endforeach
</div>
    <div class="master-meta">@include('components.icon', ['name' => 'pin']){{ $master->district }}</div>
    <div class="master-card-bottom">
<div>
<span class="muted">Работа от</span>
<strong>{{ number_format($master->base_price, 0, ',', ' ') }} ₽</strong>
</div>
<a class="round-link" href="{{ route('masters.show', $master) }}" aria-label="Профиль: {{ $master->name }}">@include('components.icon', ['name' => 'diagonal'])</a>
</div>
</article>
