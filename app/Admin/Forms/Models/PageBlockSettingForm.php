<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageBlockSetting;

class PageBlockSettingForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        $this->relationships = ['blockValue'];
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

        $this->select('page_id', __t('Page'))->options(
            Page::all()->pluck('title', 'id')->toArray()
        )->required();
        $this->select('block_id', __t('Block'))->options(
            PageBlock::all()->pluck('title', 'id')->toArray()
        )->required();

        $this->text('type', __t('Type'));
        $this->text('remark', __t('Remark'));

        $this->textarea('blockValue.schema_values', __t('Schema Values'))
            ->rows(10);

        $this->number('sort', __t('Sort'));
        $this->switch('status', __t('Status'));
    }

    protected function updateModel(int $id, array $input): void
    {
        /** @var PageBlockSetting $model */
        $model         = $this->model->findOrFail($id);
        if (! empty($input['blockValue']['schema_values'])) {
            $model->blockValue->schema_values = $input['blockValue']['schema_values'];
            $model->checkAndCreateNewBlockValue();
            unset($input['blockValue']);
        }

        $model->fill($input);
        $model->save();
    }
}
