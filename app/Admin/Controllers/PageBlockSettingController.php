<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\PageBlockSetting;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageRepository;
use App\Services\PageBlockService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class PageBlockSettingController extends AdminBaseController
{
    public function __construct(
        protected PageBlockSettingRepository $settingRepository,
        protected PageBlockRepository $blockRepository,
        protected PageRepository $pageRepository,
        protected PageBlockService $blockService
    ) {}

    protected function grid(): Grid
    {
        return Grid::make(PageBlockSetting::query()->with(['block', 'page']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('page.title', __t('Page'));
            $grid->column('block.title', __t('Block'));
            $grid->column('type', __t('Type'));
            $grid->column('template', __t('Template'));
            $grid->column('status', __t('Status'))->switch();
            $grid->column('remark', __t('Remark'));
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
            $grid->model()->orderByDesc('id');
            $grid->quickSearch('id', 'type', 'template');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->equal('page_id')->width(4);
                $filter->equal('block_id')->width(4);
                $filter->equal('type')->width(4);
                $filter->equal('status')->width(4);
                $filter->between('created_at')->datetime()->width(4);
            });
        });
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
            )->required();
            $form->text('type', __t('Type'))->required();
            $form->text('template', __t('Template'));
            $form->textarea('scripts', __t('Scripts'))->rows(2);
            $form->textarea('stylesheets', __t('Stylesheets'))->rows(2);
            $form->json('styles', __t('Styles'));
            $form->json('parameter_values', __t('Parameter Values'));
            $form->switch('status', __t('Status'));
            $form->text('remark', __t('Remark'));
            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
