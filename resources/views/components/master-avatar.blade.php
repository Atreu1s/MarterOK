<span class="master-avatar {{ $avatarClass ?? '' }}" aria-hidden="true">
@if($master->avatar_thumbnail_url)
    <img src="{{ $master->avatar_thumbnail_url }}" alt="" width="240" height="240" loading="lazy">
@else
    {{ $master->initials }}
@endif
</span>
