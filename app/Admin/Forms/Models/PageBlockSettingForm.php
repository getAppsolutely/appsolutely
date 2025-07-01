<?php

namespace App\Admin\Forms\Models;

use App\Enums\BlockScope;
use App\Models\PageBlockGroup;
use App\Models\PageBlockSetting;

class PageBlockSettingForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new PageBlockSetting();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        $this->select('block_group_id', __t('Group'))->options(
            PageBlockGroup::all()->pluck('title', 'id')->toArray()
        )->required();
        $this->text('title', __t('Title'))->required();
        $this->text('class', __t('Class'))->required();
        $this->text('remark', __t('Remark'));
        $this->textarea('description', __t('Description'))->rows(2);
        $this->textarea('template', __t('template'))->rows(3);
        $this->textarea('instruction', __t('Instruction'))->rows(2);
        $this->textarea('schema', __t('Schema'))
            ->rows(10)
            ->help(__t('Enter JSON format for block schema'));

        // Add scope field with radio buttons
        $this->radio('scope', __t('Scope'))
            ->options([
                BlockScope::Page->value   => BlockScope::Page->toArray(),
                BlockScope::Global->value => BlockScope::Global->toArray(),
            ])
            ->default(BlockScope::Page->value)
            ->required();

        // Add schema_values field as textarea
        $this->textarea('schema_values', __t('Schema Values'))
            ->rows(10)
            ->help(__t('Enter JSON format for schema values'));

        $this->switch('droppable', __t('Droppable'));
        $this->keyValue('setting', __t('Setting'))->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
        $this->number('sort', __t('Sort'));
        $this->switch('status', __t('Status'));
    }
}
