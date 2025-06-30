<section class="article-list py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{ Breadcrumbs::render('page', $page) }}
            </div>
        </div>
    </div>
    @if($page['nested'])
        @include('livewire.article-list.detail')
    @else
        @include('livewire.article-list.list')
    @endif
</section>
