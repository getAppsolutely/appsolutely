<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Models\Form;

final class FormForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new Form();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        $this->text('name', __t('Form Name'))->required()
            ->help(__t('Enter a descriptive name for the form'));

        $this->text('slug', __t('Slug'))
            ->help(__t('URL-friendly identifier. Leave empty to auto-generate from name'));

        $this->textarea('description', __t('Description'))->rows(3)
            ->help(__t('Optional description that will be shown to users'));

        $this->text('target_table', __t('Target Table'))
            ->help(__t('Optional: Database table name to also store submissions (e.g., test_drive_bookings)'));

        $this->switch('status', __t('Status'))
            ->help(__t('Enable or disable this form'));
    }
}
