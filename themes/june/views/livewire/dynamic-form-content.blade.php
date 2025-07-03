<form wire:submit.prevent="submit" novalidate>
    @csrf

    <!-- Form Fields -->
    <div class="row g-4">
        @foreach($formFields as $fieldName => $fieldConfig)
            @php
                $colClass = (!empty($displayOptions['columns']) && $displayOptions['columns'] == 2) ? 'col-md-6' : 'col-12';
                $errorName = "formData.{$fieldName}";
            @endphp

            <div class="{{ $colClass }}">
                <!-- Text Input -->
                @if(in_array($fieldConfig['type'], ['text', 'email', 'tel', 'url']))
                    <div class="form-group">
                        <label for="{{ $fieldName }}" class="form-label fw-semibold">
                            {{ $fieldConfig['label'] }}
                            @if($fieldConfig['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <input
                            type="{{ $fieldConfig['type'] }}"
                            id="{{ $fieldName }}"
                            class="form-control form-control-lg @error($errorName) is-invalid @enderror"
                            wire:model.defer="formData.{{ $fieldName }}"
                            placeholder="{{ $fieldConfig['placeholder'] ?? '' }}"
                            {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                        >
                        @error($errorName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                <!-- Date Input -->
                @elseif($fieldConfig['type'] === 'date')
                    <div class="form-group">
                        <label for="{{ $fieldName }}" class="form-label fw-semibold">
                            {{ $fieldConfig['label'] }}
                            @if($fieldConfig['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <input
                            type="date"
                            id="{{ $fieldName }}"
                            class="form-control form-control-lg @error($errorName) is-invalid @enderror"
                            wire:model.defer="formData.{{ $fieldName }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                        >
                        @error($errorName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                <!-- Select Dropdown -->
                @elseif($fieldConfig['type'] === 'select')
                    <div class="form-group">
                        <label for="{{ $fieldName }}" class="form-label fw-semibold">
                            {{ $fieldConfig['label'] }}
                            @if($fieldConfig['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <select
                            id="{{ $fieldName }}"
                            class="form-select form-select-lg @error($errorName) is-invalid @enderror"
                            wire:model.defer="formData.{{ $fieldName }}"
                            {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                        >
                            <option value="">{{ $fieldConfig['placeholder'] ?? 'Select an option' }}</option>
                            @foreach($fieldConfig['options'] ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error($errorName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                <!-- Textarea -->
                @elseif($fieldConfig['type'] === 'textarea')
                    <div class="form-group">
                        <label for="{{ $fieldName }}" class="form-label fw-semibold">
                            {{ $fieldConfig['label'] }}
                            @if($fieldConfig['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <textarea
                            id="{{ $fieldName }}"
                            class="form-control form-control-lg @error($errorName) is-invalid @enderror"
                            wire:model.defer="formData.{{ $fieldName }}"
                            rows="{{ $fieldConfig['rows'] ?? 4 }}"
                            placeholder="{{ $fieldConfig['placeholder'] ?? '' }}"
                            {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                        ></textarea>
                        @error($errorName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                <!-- Checkbox -->
                @elseif($fieldConfig['type'] === 'checkbox')
                    <div class="form-group">
                        <div class="form-check form-check-lg">
                            <input
                                type="checkbox"
                                id="{{ $fieldName }}"
                                class="form-check-input @error($errorName) is-invalid @enderror"
                                wire:model.defer="formData.{{ $fieldName }}"
                                value="1"
                                {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                            >
                            <label for="{{ $fieldName }}" class="form-check-label fw-semibold">
                                {{ $fieldConfig['label'] }}
                                @if($fieldConfig['required'] ?? false)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            @error($errorName)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                <!-- Multiple Select -->
                @elseif($fieldConfig['type'] === 'multiselect')
                    <div class="form-group">
                        <label for="{{ $fieldName }}" class="form-label fw-semibold">
                            {{ $fieldConfig['label'] }}
                            @if($fieldConfig['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <select
                            id="{{ $fieldName }}"
                            class="form-select form-select-lg @error($errorName) is-invalid @enderror"
                            wire:model.defer="formData.{{ $fieldName }}"
                            multiple
                            size="{{ min(count($fieldConfig['options'] ?? []), 5) }}"
                            {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                        >
                            @foreach($fieldConfig['options'] ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error($errorName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Hold Ctrl/Cmd to select multiple options</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Submit Button -->
    <div class="mt-5 text-center">
        <button type="submit" class="btn btn-dark btn-lg px-5 py-3" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submit">
                <i class="fas fa-calendar-check me-2"></i>{{ $displayOptions['submit_text'] }}
            </span>
            <span wire:loading wire:target="submit">
                <i class="fas fa-spinner fa-spin me-2"></i>Processing...
            </span>
        </button>
    </div>
</form>
