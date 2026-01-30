<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Enums\FormEntrySpamStatus;
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

    /**
     * Load entry with form and form.fields so Form name and formatted_data display correctly.
     */
    protected function fillModelData(int $id): void
    {
        $model = $this->model->with(['form', 'form.fields'])->find($id);

        if ($model) {
            $this->model = $model;
            $this->fill($model->toArray());
        }
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        // Basic entry information
        $this->display('form.name', __t('Form'));
        $this->display('submitted_at', __t('Submitted At'));

        $this->divider();

        // Contact information (editable)
        $this->html('<h5>' . __t('Contact Information') . '</h5>');

        $this->text('name', __t('Name'));
        $this->text('first_name', __t('First Name'));
        $this->text('last_name', __t('Last Name'));
        $this->email('email', __t('Email'));
        $this->text('mobile', __t('Mobile'));
        $this->display('user.name', __t('User'))->default(__t('Guest'));

        $this->divider();

        // Form data
        $this->html('<h5>' . __t('Form Data') . '</h5>');

        // Display formatted form data
        $this->html(function () {
            $entry = $this->model;
            if ($entry === null || ! $entry->exists) {
                return '<p>' . __t('No data available') . '</p>';
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

        // Meta information (editable where appropriate)
        $this->html('<h5>' . __t('Meta Information') . '</h5>');

        $this->switch('is_spam', __t('Spam?'))->default(FormEntrySpamStatus::Valid->value);

        $this->display('ip_address', __t('IP Address'));
        $this->text('referer', __t('Referer'));
        $this->textarea('user_agent', __t('User Agent'));
        $this->display('created_at', __t('Created At'));
        $this->display('updated_at', __t('Updated At'));
    }
}
