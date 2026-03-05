<section class="dynamic-form-interactive" data-asset-base-url="{{ asset_url(null, false) }}" wire:ignore.self>
    <!-- Background Container -->
    <div class="dynamic-form-interactive__background" wire:ignore>
        <div class="dynamic-form-interactive__background-image"></div>
        <div class="dynamic-form-interactive__background-overlay"></div>
    </div>

    <!-- Form Container -->
    <div class="dynamic-form-interactive__container">
        <div class="dynamic-form-interactive__wrapper">
            @if (!$submitted)
                <!-- Form Header -->
                @if ($displayOptions['title'] || $displayOptions['subtitle'] || $displayOptions['description'])
                    <div class="dynamic-form-interactive__header">
                        @if ($displayOptions['title'])
                            <h2 class="dynamic-form-interactive__title">{{ $displayOptions['title'] }}</h2>
                        @endif

                        @if ($displayOptions['subtitle'])
                            <h3 class="dynamic-form-interactive__subtitle">{{ $displayOptions['subtitle'] }}</h3>
                        @endif

                        @if ($displayOptions['description'])
                            <p class="dynamic-form-interactive__description">{{ $displayOptions['description'] }}</p>
                        @endif
                    </div>
                @endif

                <!-- Form Content -->
                <div class="dynamic-form-interactive__content">
                    @include('livewire.dynamic-form-content')
                </div>
            @else
                <!-- Success Message -->
                <div class="dynamic-form-interactive__success">
                    <div class="dynamic-form-interactive__success-icon">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                    </div>

                    @if ($displayOptions['success_title'])
                        <h3 class="dynamic-form-interactive__success-title">{{ $displayOptions['success_title'] }}</h3>
                    @endif

                    <p class="dynamic-form-interactive__success-message">{{ $successMessage }}</p>

                    <button wire:click="resetForm" class="dynamic-form-interactive__reset-btn">
                        <i class="fas fa-plus me-2"
                            aria-hidden="true"></i>{{ __('global.dynamic_form.submit_another_request') }}
                    </button>
                </div>
            @endif

            <!-- Error Flash Message -->
            @if (session()->has('error'))
                <div class="dynamic-form-interactive__error">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</section>
