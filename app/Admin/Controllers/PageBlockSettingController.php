<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Enums\BlockScope;
use App\Models\PageBlockSetting;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageRepository;
use App\Services\PageBlockSchemaService;
use App\Services\PageBlockService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class PageBlockSettingController extends AdminBaseController
{
    public function __construct(
        protected PageBlockSettingRepository $settingRepository,
        protected PageBlockRepository $blockRepository,
        protected PageRepository $pageRepository,
        protected PageBlockService $blockService,
        protected PageBlockSchemaService $schemaService
    ) {}

    protected function grid(): Grid
    {
        return Grid::make(PageBlockSetting::query()->with(['block', 'page']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('page.title', __t('Page'));
            $grid->column('block.title', __t('Block'));
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('sort', __t('Sort'))->editable();
            $grid->column('status', __t('Status'))->switch();

            // Filter to show only page-scoped blocks by default
            $grid->model()->whereHas('block', function ($query) {
                $query->where('scope', BlockScope::Page->value);
            });

            $grid->model()->orderByDesc('page_id');
            $grid->quickSearch('id', 'type', 'template');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->equal('page_id')->width(4);
                $filter->equal('block_id')->width(4);
                $filter->equal('type')->width(4);
                $filter->equal('status')->width(4);
                $filter->between('created_at')->datetime()->width(4);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });

            // Add quick links for global blocks
            $grid->header(function () {
                return $this->renderGlobalBlocksQuickLinks();
            });
        });
    }

    /**
     * Render global blocks quick links section
     * Can be reused in other controllers or views
     */
    public function renderGlobalBlocksQuickLinks(): string
    {
        $globalBlocks = $this->blockRepository->getGlobalBlocks();

        if ($globalBlocks->isEmpty()) {
            return '';
        }

        $links = $globalBlocks->map(function ($block) {
            $url = admin_route('pages.blocks.edit', ['block' => $block->id]);

            return "<a href='{$url}' class='btn btn-sm btn-primary mr-2' target='_blank' style='text-decoration: none;'>
                <i class='fa fa-cog mr-1'></i>{$block->title}
            </a>";
        })->join('');

        return "<div class='alert alert-info border-0 mt-1'>
            <div class='d-flex align-items-center mb-1'>
                <i class='fa fa-globe text-info mr-2'></i>
                <strong>Global Blocks Available</strong>
            </div>
            <p class='text-dark mb-1'>Click on any block below to quickly edit its global settings:</p>
            <div class='d-flex flex-wrap'>
                {$links}
            </div>
        </div>";
    }

    protected function form(): Form
    {
        return Form::make(PageBlockSetting::query()->with(['blockValue']), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->select('page_id', __t('Page'))->options(
                $this->pageRepository->all()->pluck('title', 'id')->toArray()
            )->required();
            $form->select('block_id', __t('Block'))->options(
                $this->blockRepository->all()->pluck('title', 'id')->toArray()
            )->required();

            $form->text('type', __t('Type'));
            $form->text('remark', __t('Remark'));

            $form->textarea('_schema_values', __t('Schema Values'))
                ->value($form->model()->blockValue?->schema_values ?? '')
                ->rows(10);

            if ($this->ifGlobalBlock($form)) {
                $this->addGlobalBlockInfo($form, $form->model()->block);
            }

            $form->number('sort', __t('Sort'));
            $form->switch('status', __t('Status'));
            $form->disableViewButton();
            $form->disableViewCheck();

            $form->saving(function (Form $form) {
                $model        = $form->model();
                $schemaValues =  $form->_schema_values;

                if (! empty($schemaValues)) {
                    $model->blockValue->schema_values = $schemaValues;
                    $model->checkAndCreateNewBlockValue();
                }
            });
            // $form->ignore(['_schema_values']);
        });
    }

    private function ifGlobalBlock(Form $form)
    {
        // Show schema_values field for new records or page-scoped blocks
        if (! $form->isEditing() ||
            ! $form->model()->block ||
            $form->model()->block->scope === BlockScope::Page->value) {

            return false;
        }

        return true;
    }

    /**
     * Add global block information with edit link
     */
    private function addGlobalBlockInfo(Form $form, $block): void
    {
        $blockEditUrl = admin_route('pages.blocks.edit', ['block' => $block->id]);

        $form->html(sprintf(
            '<div class="alert alert-info">
                <strong>%s</strong> %s
                <a href="%s" class="btn btn-primary btn-sm ml-2" target="_blank">
                    %s
                </a>
            </div>',
            __('Global Block:'),
            __('This block uses global settings.'),
            $blockEditUrl,
            __('Edit Block Settings')
        ));
    }
}
