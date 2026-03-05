<div class="article-list__container container">
    <!-- Header -->
    @if ($displayOptions['title'] ?? false)
        <div class="article-list__header text-center mb-5">
            <h2 class="article-list__title display-4 fw-bold text-dark mb-3">
                {{ $displayOptions['title'] }}
            </h2>
            @if ($displayOptions['subtitle'] ?? false)
                <p class="article-list__subtitle lead text-muted">
                    {{ $displayOptions['subtitle'] }}
                </p>
            @endif
        </div>
    @endif

    <!-- Articles Grid -->
    @if ($articles->count() > 0)
        <div class="article-list__grid row g-4">
            @foreach ($articles as $article)
                <div class="article-list__item col-lg-4 col-md-6">
                    <article class="article-list__card card h-100 border-0 shadow-sm">
                        <!-- Featured Image -->
                        @if (($displayOptions['show_featured_image'] ?? true) && $article->cover)
                            <div class="article-list__card-image card-img-top position-relative">
                                <img src="{{ $article->cover }}" class="article-list__card-img card-img-top"
                                    alt="{{ $article->title }}" style="height: 200px; object-fit: cover;">
                                @if ($article->categories->first())
                                    <span
                                        class="article-list__card-badge badge bg-dark position-absolute top-0 start-0 m-3">
                                        {{ $article->categories->first()->name }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="article-list__card-body card-body d-flex flex-column">
                            <!-- Meta Information -->
                            @if (($displayOptions['show_author'] ?? true) || ($displayOptions['show_date'] ?? true))
                                <div class="article-list__card-meta d-flex align-items-center text-muted small mb-2">
                                    @if (($displayOptions['show_author'] ?? true) && $article->author)
                                        <span class="me-3">
                                            <i class="fas fa-user me-1" aria-hidden="true"></i>
                                            {{ $article->author }}
                                        </span>
                                    @endif
                                    @if (($displayOptions['show_date'] ?? true) && $article->published_at)
                                        <span>
                                            <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i>
                                            {{ $article->published_at->format('M j, Y') }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Title -->
                            <h5 class="article-list__card-title card-title fw-bold mb-3">
                                <a href="{{ nested_url($article->slug) }}"
                                    class="article-list__card-link text-decoration-none text-dark">
                                    {{ $article->title }}
                                </a>
                            </h5>

                            <!-- Excerpt -->
                            @if (($displayOptions['show_excerpt'] ?? true) && $article->description)
                                <p class="article-list__card-excerpt card-text text-muted mb-3 flex-grow-1">
                                    {{ Str::limit($article->description, 120) }}
                                </p>
                            @endif

                            <!-- Read More Button -->
                            @if ($displayOptions['show_read_more'] ?? true)
                                <div class="article-list__card-actions mt-auto">
                                    <a href="{{ nested_url($article->slug) }}"
                                        class="article-list__card-btn btn btn-outline-dark btn-sm">
                                        {{ $displayOptions['read_more_text'] ?? 'Read More' }}
                                        <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($articles->hasPages())
            <div class="article-list__pagination d-flex justify-content-center mt-5">
                {{ $articles->links() }}
            </div>
        @endif
    @else
        <!-- No Articles Message -->
        <div class="article-list__empty text-center py-5">
            <div class="article-list__empty-icon mb-4">
                <i class="fas fa-newspaper fa-3x text-muted" aria-hidden="true"></i>
            </div>
            <h4 class="article-list__empty-title text-muted">No articles found</h4>
            <p class="article-list__empty-text text-muted">Check back later for new content.</p>
        </div>
    @endif
</div>
