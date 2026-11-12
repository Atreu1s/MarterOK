@php
    $unread = auth()->user()->unreadNotifications()
        ->where('type', \App\Notifications\NewArticleNotification::class);
    $unreadCount = $unread->count();
@endphp
<details class="article-notifications">
    <summary aria-label="Непрочитанные уведомления: {{ $unreadCount }}">
        @include('components.icon', ['name' => 'message'])
        <span aria-hidden="true">{{ $unreadCount }}</span>
    </summary>
    <div class="article-notifications-list">
        @forelse($unread->limit(10)->get() as $notification)
            <form method="post" action="{{ route('notifications.read', $notification->id) }}">
                @csrf
                <button type="submit">{{ $notification->data['title'] }}</button>
            </form>
        @empty
            <p>Новых уведомлений нет.</p>
        @endforelse
    </div>
</details>
