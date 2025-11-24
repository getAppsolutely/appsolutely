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

        $this->textarea('blockValue.display_options', __t('Display Options'))
            ->rows(10)->help(__t('JSON format for display options'));

        $this->textarea('blockValue.query_options', __t('Query Options'))
            ->rows(10)->help(__t('JSON format for query options'));

        $this->number('sort', __t('Sort'));
        $this->switch('status', __t('Status'));
    }

    protected function updateModel(int $id, array $input): void
    {
        /** @var PageBlockSetting $model */
        $model = $this->model->findOrFail($id);

        // Handle block value updates
        if (! empty($input['blockValue'])) {
            $blockValueChanged = false;

            if (isset($input['blockValue']['display_options'])) {
                $model->blockValue->display_options = $input['blockValue']['display_options'];
                $blockValueChanged                  = true;
            }

            if (isset($input['blockValue']['query_options'])) {
                $model->blockValue->query_options = $input['blockValue']['query_options'];
                $blockValueChanged                = true;
            }

            if ($blockValueChanged) {
                $model->checkAndCreateNewBlockValue();
            }
            unset($input['blockValue']);
        }

        $model->fill($input);
        $model->save();
    }
}
