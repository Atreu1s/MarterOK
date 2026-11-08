@if($paginator->hasPages())<nav class="pagination" aria-label="Страницы списка">
<span>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} из {{ $paginator->total() }}</span>
<div>
@if($paginator->onFirstPage())<span class="page-button disabled" aria-disabled="true">@include('components.icon', ['name' => 'back'])</span>@else
<a class="page-button" href="{{ $paginator->previousPageUrl() }}" aria-label="Предыдущая страница" rel="prev">@include('components.icon', ['name' => 'back'])</a>@endif @foreach($paginator->getUrlRange(max(1, $paginator->currentPage()-2), min($paginator->lastPage(), $paginator->currentPage()+2)) as $page => $url)<a class="page-button" href="{{ $url }}" @if($page === $paginator->currentPage()) aria-current="page" @endif>{{ $page }}</a>@endforeach @if($paginator->hasMorePages())<a class="page-button" href="{{ $paginator->nextPageUrl() }}" aria-label="Следующая страница" rel="next">@include('components.icon', ['name' => 'arrow'])</a>@else
<span class="page-button disabled" aria-disabled="true">@include('components.icon', ['name' => 'arrow'])</span>@endif
</div>
</nav>@endif
