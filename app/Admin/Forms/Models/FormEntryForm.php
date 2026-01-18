<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Models\FormEntry;

final class FormEntryForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new FormEntry();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        // Basic entry information
        $this->display('form.name', __t('Form'));
        $this->display('submitted_at', __t('Submitted At'));

        $this->divider();

        // Contact information
        $this->html('<h5>' . __t('Contact Information') . '</h5>');

        $this->display('name', __t('Name'));
        $this->display('first_name', __t('First Name'));
        $this->display('last_name', __t('Last Name'));
        $this->display('email', __t('Email'));
        $this->display('mobile', __t('Mobile'));
        $this->display('user.name', __t('User'))->default(__t('Guest'));

        $this->divider();

        // Form data
        $this->html('<h5>' . __t('Form Data') . '</h5>');

        // Display formatted form data
        $this->html(function () {
            $entry = $this->model;
            if (! $entry->exists) {
                return '<p>No data available</p>';
            }

            $html = '<div class="table-responsive"><table class="table table-bordered">';
            $html .= '<thead><tr><th>Field</th><th>Value</th></tr></thead><tbody>';

            foreach ($entry->formatted_data as $label => $value) {
                $html .= "<tr><td><strong>{$label}</strong></td><td>{$value}</td></tr>";
            }

            $html .= '</tbody></table></div>';

            return $html;
        });

        $this->divider();

        // Meta information
        $this->html('<h5>' . __t('Meta Information') . '</h5>');

        $this->display('is_spam', __t('Status'))->as(function ($isSpam) {
            return $isSpam ? '<span class="badge badge-danger">Spam</span>' : '<span class="badge badge-success">Valid</span>';
        });

        $this->display('ip_address', __t('IP Address'));
        $this->display('referer', __t('Referer'));
        $this->display('user_agent', __t('User Agent'))->limit(100);
        $this->display('created_at', __t('Created At'));
        $this->display('updated_at', __t('Updated At'));

        // Add spam toggle buttons
        $this->html(function () {
            $entry = $this->model;
            if (! $entry->exists) {
                return '';
            }

            $spamButton = $entry->is_spam
                ? '<button type="button" class="btn btn-success" onclick="toggleSpamStatus(' . $entry->id . ', false)"><i class="fa fa-check"></i> Mark as Valid</button>'
                : '<button type="button" class="btn btn-warning" onclick="toggleSpamStatus(' . $entry->id . ', true)"><i class="fa fa-ban"></i> Mark as Spam</button>';

            return '<div class="mt-3">' . $spamButton . '</div>';
        });

        // Disable all form buttons since this is view-only
        $this->disableSubmitButton();
        $this->disableResetButton();
    }
}
