<section class="dynamic-form-interactive" data-asset-base-url="{{ asset_url(null, false) }}" wire:ignore.self>
    <!-- Background Container -->
    <div class="dynamic-form-background" wire:ignore>
        <div class="dynamic-form-background-image"></div>
        <div class="dynamic-form-background-overlay"></div>
    </div>

    <!-- Form Container -->
    <div class="dynamic-form-container">
        <div class="dynamic-form-wrapper">
            @if (!$submitted)
                <!-- Form Header -->
                @if ($displayOptions['title'] || $displayOptions['subtitle'] || $displayOptions['description'])
                    <div class="dynamic-form-header">
                        @if ($displayOptions['title'])
                            <h2 class="dynamic-form-title">{{ $displayOptions['title'] }}</h2>
                        @endif

                        @if ($displayOptions['subtitle'])
                            <h3 class="dynamic-form-subtitle">{{ $displayOptions['subtitle'] }}</h3>
                        @endif

                        @if ($displayOptions['description'])
                            <p class="dynamic-form-description">{{ $displayOptions['description'] }}</p>
                        @endif
                    </div>
                @endif

                <!-- Form Content -->
                <div class="dynamic-form-content-wrapper">
                    @include('livewire.dynamic-form-content')
                </div>
            @else
                <!-- Success Message -->
                <div class="dynamic-form-success">
                    <div class="dynamic-form-success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>

                    @if ($displayOptions['success_title'])
                        <h3 class="dynamic-form-success-title">{{ $displayOptions['success_title'] }}</h3>
                    @endif

                    <p class="dynamic-form-success-message">{{ $successMessage }}</p>

                    <button wire:click="resetForm" class="dynamic-form-reset-btn">
                        <i class="fas fa-plus me-2"></i>Submit Another Request
                    </button>
                </div>
            @endif

            <!-- Error Flash Message -->
            @if (session()->has('error'))
                <div class="dynamic-form-error">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</section>
