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
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
            $grid->column('sort', __t('Sort'))->editable();
            $grid->column('status', __t('Status'))->switch();

            // Filter to show only page-scoped blocks by default
            $grid->model()->whereHas('block', function ($query) {
                $query->where('scope', BlockScope::Page->value);
            });

            $grid->model()->orderByDesc('page_id')->orderBy('sort');
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
        return Form::make(PageBlockSetting::query(), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->select('page_id', __t('Page'))->options(
                $this->pageRepository->all()->pluck('title', 'id')->toArray()
            )->required();
            $form->select('block_id', __t('Block'))->options(
                $this->blockRepository->all()->pluck('title', 'id')->toArray()
            )->required()->load('schema_fields', admin_route('api.pages.block.schema-fields'));

            $form->text('type', __t('Type'));
            $form->text('remark', __t('Remark'));

            $this->addSchemaValuesField($form);

            $form->textarea('template', __t('Template'))->rows(3);
            $form->textarea('scripts', __t('Scripts'))->rows(2);
            $form->textarea('stylesheets', __t('Stylesheets'))->rows(2);
            $form->keyValue('styles', __t('Styles'))->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
            $form->number('sort', __t('Sort'));
            $form->switch('status', __t('Status'));
            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }

    /**
     * Add schema values field based on block scope
     */
    private function addSchemaValuesField(Form $form): void
    {
        // Show schema_values field for new records or page-scoped blocks
        if (! $form->isEditing() ||
            ! $form->model()->block ||
            $form->model()->block->scope === BlockScope::Page->value) {
            $form->textarea('schema_values', __t('Schema Values'))->rows(10);

            return;
        }

        // Show global block info for global-scoped blocks
        $this->addGlobalBlockInfo($form, $form->model()->block);
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
