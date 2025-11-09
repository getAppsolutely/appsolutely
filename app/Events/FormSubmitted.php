<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a form is submitted
 */
final class FormSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Form $form,
        public readonly FormEntry $entry,
        public readonly array $data
    ) {}
}
