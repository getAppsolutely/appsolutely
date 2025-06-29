<section class="dynamic-form-section py-5">
    <div class="container">
        @if(!$submitted)
            <!-- Form Header -->
            @if($displayOptions['title'] || $displayOptions['subtitle'] || $displayOptions['description'])
                <div class="text-center mb-5">
                    @if($displayOptions['title'])
                        <h2 class="display-6 fw-bold mb-3">{{ $displayOptions['title'] }}</h2>
                    @endif

                    @if($displayOptions['subtitle'])
                        <h3 class="h5 text-muted mb-4">{{ $displayOptions['subtitle'] }}</h3>
                    @endif

                    @if($displayOptions['description'])
                        <p class="lead text-muted">{{ $displayOptions['description'] }}</p>
                    @endif
                </div>
            @endif

            <!-- Form Container -->
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    @if($displayOptions['theme'] === 'card')
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-5">
                                @include('livewire.dynamic-form-content')
                            </div>
                        </div>
                    @else
                        <div class="form-container">
                            @include('livewire.dynamic-form-content')
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Success Message -->
            <div class="text-center py-5">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="success-message">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>

                            @if($displayOptions['success_title'])
                                <h3 class="h2 fw-bold text-dark mb-3">{{ $displayOptions['success_title'] }}</h3>
                            @endif

                            <p class="lead text-muted mb-4">{{ $successMessage }}</p>

                            <button wire:click="resetForm" class="btn btn-outline-dark btn-lg px-4">
                                <i class="fas fa-plus me-2"></i>Submit Another Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Flash Message -->
        @if(session()->has('error'))
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8 col-xl-6">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
