<div class="container">
    <!-- Header -->
    @if($data['title'] ?? false)
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark mb-3">
                {{ $data['title'] }}
            </h2>
            @if($data['subtitle'] ?? false)
                <p class="lead text-muted">
                    {{ $data['subtitle'] }}
                </p>
            @endif
        </div>
    @endif

    <!-- Articles Grid -->
    @if($articles->count() > 0)
        <div class="row g-4">
            @foreach($articles as $article)
                <div class="col-lg-4 col-md-6">
                    <article class="card h-100 border-0 shadow-sm article-card">
                        <!-- Featured Image -->
                        @if(($data['show_featured_image'] ?? true) && $article->cover)
                            <div class="card-img-top position-relative">
                                <img src="{{ $article->cover }}"
                                     class="card-img-top"
                                     alt="{{ $article->title }}"
                                     style="height: 200px; object-fit: cover;">
                                @if($article->categories->first())
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-3">
                                        {{ $article->categories->first()->name }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <!-- Meta Information -->
                            @if(($data['show_author'] ?? true) || ($data['show_date'] ?? true))
                                <div class="d-flex align-items-center text-muted small mb-2">
                                    @if(($data['show_author'] ?? true) && $article->author)
                                        <span class="me-3">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $article->author }}
                                        </span>
                                    @endif
                                    @if(($data['show_date'] ?? true) && $article->published_at)
                                        <span>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $article->published_at->format('M j, Y') }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Title -->
                            <h5 class="card-title fw-bold mb-3">
                                <a href="{{ nested_url($article->slug) }}"
                                   class="text-decoration-none text-dark">
                                    {{ $article->title }}
                                </a>
                            </h5>

                            <!-- Excerpt -->
                            @if(($data['show_excerpt'] ?? true) && $article->description)
                                <p class="card-text text-muted mb-3 flex-grow-1">
                                    {{ Str::limit($article->description, 120) }}
                                </p>
                            @endif

                            <!-- Read More Button -->
                            @if($data['show_read_more'] ?? true)
                                <div class="mt-auto">
                                    <a href="{{ nested_url($article->slug) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        {{ $data['read_more_text'] ?? 'Read More' }}
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $articles->links() }}
            </div>
        @endif
    @else
        <!-- No Articles Message -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-newspaper fa-3x text-muted"></i>
            </div>
            <h4 class="text-muted">No articles found</h4>
            <p class="text-muted">Check back later for new content.</p>
        </div>
    @endif
</div>

