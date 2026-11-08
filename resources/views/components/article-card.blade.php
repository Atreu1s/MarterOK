<article class="article-card reveal">
<a class="article-cover image-frame" href="{{ route('articles.show', $article) }}">
<img src="{{ $article->cover_url }}" alt="" loading="lazy" width="640" height="360">
<span class="image-arrow">@include('components.icon', ['name' => 'diagonal'])</span>
</a>
<div class="article-card-meta">
<span>{{ $article->category }}</span>
<span>{{ $article->reading_minutes }} мин чтения</span>
@if($article->status === 'draft')<span class="status-badge">Черновик</span>@endif
</div>
<h3>
<a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a>
</h3>
<p>{{ $article->excerpt }}</p>
</article>
