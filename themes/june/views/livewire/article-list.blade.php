<section class="article-list py-5">
    @if($page['nested'])
        @include('livewire.article-list.detail')
    @else
        @include('livewire.article-list.list')
    @endif
</section>
