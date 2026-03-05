@php
    $article = ($page['model'] ?? null) instanceof \App\Models\Article ? $page['model'] : null;
@endphp
<div class="article-list__detail container">
    <div class="article-list__detail-row row justify-content-center">
        <div class="article-list__detail-content col-lg-10">
            <!-- Title -->
            @if ($article?->title ?? false)
                <h1 class="article-list__detail-title fw-bold text-dark mb-3">
                    {{ $article->title }}
                </h1>
            @endif

            <!-- Subtitle -->
            @if ($article?->subtitle ?? false)
                <p class="article-list__detail-subtitle lead text-muted mb-4">
                    {{ $article->subtitle }}
                </p>
            @endif

            <!-- Meta Information -->
            @if (($article?->show_meta ?? true) && ($article?->published_at ?? false))
                <div class="article-list__detail-meta text-muted mb-4 pb-3 border-bottom">
                    <small>
                        @if ($article->published_at ?? false)
                            <time datetime="{{ $article->published_at }}">
                                Published: {{ \Carbon\Carbon::parse($article->published_at)->format('F j, Y') }}
                            </time>
                        @endif
                    </small>
                </div>
            @endif

            <!-- Content -->
            @if ($article?->content ?? false)
                <div class="article-list__detail-body content-body markdown">
                    {!! md2html($article->content) !!}
                </div>
            @endif
        </div>
    </div>
</div>
