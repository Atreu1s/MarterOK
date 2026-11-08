<section class="work-examples" id="examples">
    <div class="section-heading reveal">
        <div><p class="eyebrow">За рабочим столом</p><h2>От симптома<br>к решению.</h2></div>
        <p class="section-intro">Типовые задачи и последовательность работы.<br>Точный план появляется после диагностики.</p>
    </div>
    <div class="work-example-grid">
        @foreach($examples as $example)
            <article class="work-example reveal">
                <a class="image-frame" href="{{ asset($example['image']) }}" data-lightbox data-caption="{{ $example['title'] }}">
                    <img src="{{ asset($example['image']) }}" alt="{{ $example['title'] }}: работа в мастерской" width="1440" height="960" loading="lazy">
                    <span class="image-arrow">@include('components.icon', ['name' => 'diagonal'])</span>
                </a>
                <div class="work-example-body">
                    <span class="small-label">Задача 0{{ $loop->iteration }}</span>
                    <h3>{{ $example['title'] }}</h3>
                    <p>{{ $example['symptom'] }}</p>
                    <details><summary>Что проверяет мастер @include('components.icon', ['name' => 'plus'])</summary>
                        <ol>@foreach($example['steps'] as $step)<li>{{ $step }}</li>@endforeach</ol>
                        <p>{{ $example['result'] }}</p>
                    </details>
                </div>
            </article>
        @endforeach
    </div>
</section>
