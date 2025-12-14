<section class="block dynamic-form dynamic-form-interactive position-relative overflow-hidden">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Left Side: Dynamic Background Image -->
            <div class="col-lg-6 position-relative" id="vehicle-background-container">
                <div class="vehicle-background-image position-absolute top-0 start-0 w-100"
                    style="height: 100%; background-size: cover; background-position: center; background-repeat: no-repeat; transition: opacity 0.5s ease-in-out;">
                </div>
                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="background: linear-gradient(135deg, rgba(255, 165, 0, 0.1) 0%, rgba(255, 140, 0, 0.2) 100%);">
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="col-lg-6 d-flex align-items-center">
                <div class="w-100 p-3 p-lg-4" style="background-color: #f8f9fa;">
                    <div class="w-100 mx-auto" style="max-width: 500px;">
                        @if (!$submitted)
                            <!-- Title and Description -->
                            @if ($displayOptions['title'] || $displayOptions['subtitle'] || $displayOptions['description'])
                                <div class="mb-3">
                                    @if ($displayOptions['title'])
                                        <h2 class="h4 fw-bold mb-2">{{ $displayOptions['title'] }}</h2>
                                    @endif
                                    @if ($displayOptions['subtitle'])
                                        <h3 class="h6 text-muted mb-2">{{ $displayOptions['subtitle'] }}</h3>
                                    @endif
                                    @if ($displayOptions['description'])
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                            {{ $displayOptions['description'] }}</p>
                                    @endif
                                </div>
                            @endif

                            @include('livewire.dynamic-form-content')
                        @else
                            <!-- Success Message -->
                            <div class="text-center py-5">
                                <div class="success-message">
                                    <div class="mb-4">
                                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                    </div>

                                    @if ($displayOptions['success_title'])
                                        <h3 class="h2 fw-bold text-dark mb-3">{{ $displayOptions['success_title'] }}
                                        </h3>
                                    @endif

                                    <p class="lead text-muted mb-4">{{ $successMessage }}</p>

                                    <button wire:click="resetForm" class="btn btn-outline-dark btn-lg px-4">
                                        <i class="fas fa-plus me-2"></i>Submit Another Request
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Error Flash Message -->
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
