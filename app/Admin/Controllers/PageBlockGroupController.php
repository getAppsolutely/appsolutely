<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\PageBlockGroup;
use App\Repositories\PageBlockGroupRepository;
use App\Services\PageBlockService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class PageBlockGroupController extends AdminBaseController
{
    public function __construct(
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockService $blockService
    ) {}

    protected function grid(): Grid
    {
        return Grid::make(PageBlockGroup::query(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
            $grid->column('sort', __t('Sort'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');
            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->like('title')->width(4);
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
        return Form::make(PageBlockGroup::query(), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->text('title', __t('Title'))->required();
            $form->text('remark', __t('Remark'));
            $form->number('sort', __t('Sort'));
            $form->switch('status', __t('Status'));
            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
