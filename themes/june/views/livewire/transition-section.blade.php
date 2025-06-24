<section
    class="transition-section position-relative w-100"
    style="{{ $this->getBackgroundStyle() }}"
    @if(isset($data['responsive']['hide_on_mobile']) && $data['responsive']['hide_on_mobile'])
        data-hide-mobile="true"
    @endif
>
    <!-- Background Overlay -->
    @if(isset($data['overlay']['enabled']) && $data['overlay']['enabled'])
        <div class="position-absolute top-0 start-0 w-100 h-100"
             style="background-color: {{ $data['overlay']['color'] ?? '#000000' }}; opacity: {{ $data['overlay']['opacity'] ?? 0.3 }};"></div>
    @endif
</section>

