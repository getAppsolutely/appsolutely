@php
    $style = $displayOptions['style'] ?? 'fullscreen';
    $viewName = 'livewire.media-slider_' . $style;
@endphp

<div>
@if(View::exists($viewName))
    @include($viewName)
@else
    <div class="alert alert-warning">Media slider view not found: {{ $viewName }}</div>
@endif
</div>
