<section class="faq-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(($displayOptions['title'] ?? false) || ($displayOptions['subtitle'] ?? false))
                    <div class="text-center mb-5">
                        @if($displayOptions['title'] ?? false)
                            <h2 class="display-5 fw-bold mb-3">{{ $displayOptions['title'] }}</h2>
                        @endif
                        @if($displayOptions['subtitle'] ?? false)
                            <p class="lead text-muted">{{ $displayOptions['subtitle'] }}</p>
                        @endif
                    </div>
                @endif

                @if(!empty($displayOptions['items']) && is_array($displayOptions['items']))
                    <div class="accordion" id="faqAccordion">
                        @foreach($displayOptions['items'] as $index => $item)
                            @if(!empty($item['question']))
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#faq{{ $index }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="faq{{ $index }}">
                                            {{ $item['question'] }}
                                        </button>
                                    </h3>
                                    <div id="faq{{ $index }}"
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                         data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {!! md2html((string) ($item['answer'] ?? '')) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
