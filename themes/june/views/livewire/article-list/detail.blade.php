<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Title -->
            @if($model->title ?? false)
                <h1 class="fw-bold text-dark mb-3">
                    {{ $model->title }}
                </h1>
            @endif

            <!-- Subtitle -->
            @if($model->subtitle ?? false)
                <p class="lead text-muted mb-4">
                    {{ $model->subtitle }}
                </p>
            @endif

            <!-- Meta Information -->
            @if(($model->show_meta ?? true) && ($model->published_at ?? false))
                <div class="text-muted mb-4 pb-3 border-bottom">
                    <small>
                        @if($model->published_at ?? false)
                            <time datetime="{{ $model->published_at }}">
                                Published: {{ \Carbon\Carbon::parse($model->published_at)->format('F j, Y') }}
                            </time>
                        @endif
                    </small>
                </div>
            @endif

            <!-- Content -->
            @if($model->content ?? false)
                <div class="content-body">
                    {!! md2html($model->content) !!}
                </div>
            @endif
        </div>
    </div>
</div>
