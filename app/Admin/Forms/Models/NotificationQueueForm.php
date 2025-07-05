<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Models\NotificationQueue;

final class NotificationQueueForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new NotificationQueue();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        // Basic queue information
        $this->display('template.name', __t('Template'));
        $this->display('rule.name', __t('Rule'));
        $this->display('status', __t('Status'))->as(function ($status) {
            $colors = [
                'pending'   => 'warning',
                'sent'      => 'success',
                'failed'    => 'danger',
                'cancelled' => 'secondary',
            ];
            $labels = [
                'pending'   => __t('Pending'),
                'sent'      => __t('Sent'),
                'failed'    => __t('Failed'),
                'cancelled' => __t('Cancelled'),
            ];

            return "<span class='badge badge-{$colors[$status]}'>{$labels[$status]}</span>";
        });

        $this->divider();

        $this->html('<h5>' . __t('Email Details') . '</h5>');

        $this->display('recipient_email', __t('Recipient Email'));
        $this->display('recipient_name', __t('Recipient Name'));
        $this->display('subject', __t('Subject'));
        $this->display('from_email', __t('From Email'));
        $this->display('from_name', __t('From Name'));
        $this->display('reply_to', __t('Reply To'));

        $this->divider();

        $this->html('<h5>' . __t('Email Content') . '</h5>');

        $this->textarea('body_html', __t('HTML Body'))->rows(10)->readonly();
        $this->textarea('body_text', __t('Text Body'))->rows(8)->readonly();

        $this->divider();

        $this->html('<h5>' . __t('Variables & Data') . '</h5>');

        // Display variables in a formatted way
        $this->html(function () {
            $queue = $this->model;
            if (! $queue->exists || empty($queue->variables)) {
                return '<p>' . __t('No variables available') . '</p>';
            }

            $html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
            $html .= '<thead><tr><th>' . __t('Variable') . '</th><th>' . __t('Value') . '</th></tr></thead><tbody>';

            foreach ($queue->variables as $key => $value) {
                $displayValue = is_array($value) || is_object($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
                $html .= "<tr><td><code>{{$key}}</code></td><td>" . htmlspecialchars($displayValue) . '</td></tr>';
            }

            $html .= '</tbody></table></div>';

            return $html;
        });

        $this->divider();

        $this->html('<h5>' . __t('Timing & Attempts') . '</h5>');

        $this->display('scheduled_at', __t('Scheduled At'));
        $this->display('sent_at', __t('Sent At'));
        $this->display('failed_at', __t('Failed At'));
        $this->display('retry_count', __t('Retry Count'));
        $this->display('max_attempts', __t('Max Attempts'));
        $this->display('priority', __t('Priority'))->as(function ($priority) {
            $colors = [
                'low'    => 'secondary',
                'normal' => 'primary',
                'high'   => 'warning',
                'urgent' => 'danger',
            ];

            return "<span class='badge badge-{$colors[$priority]}'>" . ucfirst($priority) . '</span>';
        });

        $this->divider();

        $this->html('<h5>' . __t('Error Information') . '</h5>');

        $this->textarea('error_message', __t('Error Message'))->rows(4)->readonly();
        $this->textarea('error_details', __t('Error Details'))->rows(6)->readonly();

        $this->divider();

        $this->html('<h5>' . __t('Meta Information') . '</h5>');

        $this->display('created_at', __t('Created At'));
        $this->display('updated_at', __t('Updated At'));

        // Display metadata in a formatted way
        $this->html(function () {
            $queue = $this->model;
            if (! $queue->exists || empty($queue->metadata)) {
                return '<p>' . __t('No metadata available') . '</p>';
            }

            $html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
            $html .= '<thead><tr><th>' . __t('Key') . '</th><th>' . __t('Value') . '</th></tr></thead><tbody>';

            foreach ($queue->metadata as $key => $value) {
                $displayValue = is_array($value) || is_object($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
                $html .= "<tr><td><strong>{$key}</strong></td><td>" . htmlspecialchars($displayValue) . '</td></tr>';
            }

            $html .= '</tbody></table></div>';

            return $html;
        });

        // Add action buttons for queue management
        $this->html(function () {
            $queue = $this->model;
            if (! $queue->exists) {
                return '';
            }

            $buttons = '<div class="mt-3">';

            if ($queue->status === 'failed') {
                $buttons .= '<button type="button" class="btn btn-success mr-2" onclick="retryNotification(' . $queue->id . ')"><i class="fa fa-retry"></i> ' . __t('Retry') . '</button>';
            }

            if ($queue->status === 'pending') {
                $buttons .= '<button type="button" class="btn btn-warning mr-2" onclick="cancelNotification(' . $queue->id . ')"><i class="fa fa-ban"></i> ' . __t('Cancel') . '</button>';
            }

            if (in_array($queue->status, ['sent', 'failed', 'cancelled'])) {
                $buttons .= '<button type="button" class="btn btn-primary mr-2" onclick="duplicateNotification(' . $queue->id . ')"><i class="fa fa-copy"></i> ' . __t('Duplicate') . '</button>';
            }

            $buttons .= '</div>';

            return $buttons;
        });

        // Disable all form buttons since this is view-only
        $this->disableSubmitButton();
        $this->disableResetButton();
    }
}
