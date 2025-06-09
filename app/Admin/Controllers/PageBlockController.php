<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\PageBlock;
use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Services\PageBlockService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class PageBlockController extends AdminBaseController
{
    public function __construct(
        protected PageBlockRepository $blockRepository,
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockService $blockService
    ) {}

    protected function grid(): Grid
    {
        return Grid::make(PageBlock::query()->with('group'), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('reference', __t('reference'));
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('group.title', __t('Group'));
            $grid->column('class', __t('Class'));
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
            $grid->column('sort', __t('Sort'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');
            $grid->quickSearch('id', 'title', 'class');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->like('title')->width(4);
                $filter->equal('block_group_id')->width(4);
                $filter->equal('status')->width(4);
                $filter->between('created_at')->datetime()->width(4);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    protected function form(): Form
    {
        return Form::make(PageBlock::query(), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->select('block_group_id', __t('Group'))->options(
                $this->groupRepository->all()->pluck('title', 'id')->toArray()
            )->required();
            $form->text('title', __t('Title'))->required();
            $form->text('class', __t('Class'))->required();
            $form->text('remark', __t('Remark'));
            $form->textarea('description', __t('Description'))->rows(2);
            $form->textarea('template', __t('template'))->rows(3);
            $form->textarea('instruction', __t('Instruction'))->rows(2);
            $form->keyValue('parameters', __t('Parameters'))->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
            $form->keyValue('setting', __t('Setting'))->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
            $form->number('sort', __t('Sort'));
            $form->switch('status', __t('Status'));
            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
