<section 
    class="transition-section position-relative w-100"
    style="{{ $this->getBackgroundStyle() }}"
    @if(isset($transitionSection['responsive']['hide_on_mobile']) && $transitionSection['responsive']['hide_on_mobile']) 
        data-hide-mobile="true" 
    @endif
>
    <!-- Background Overlay -->
    @if(isset($transitionSection['overlay']['enabled']) && $transitionSection['overlay']['enabled'])
        <div class="position-absolute top-0 start-0 w-100 h-100" 
             style="background-color: {{ $transitionSection['overlay']['color'] ?? '#000000' }}; opacity: {{ $transitionSection['overlay']['opacity'] ?? 0.3 }};"></div>
    @endif
</section>

 