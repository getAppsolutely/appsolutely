<?php

namespace App\Admin\Controllers;

use App\Models\MenuGroup;
use App\Repositories\MenuGroupRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class MenuGroupController extends AdminBaseController
{
    public function __construct(protected MenuGroupRepository $menuGroupRepository) {}

    protected function grid(): Grid
    {
        return Grid::make(MenuGroup::query(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'));
            $grid->column('reference', __t('Reference'))->copyable();
            $grid->column('remark', __t('Remark'));
            $grid->column('status', __t('Status'))->switch();
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
            $grid->column('updated_at', __t('Updated At'))->display(column_time_format());
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title', 'reference', 'remark');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('title')->width(3);
                $filter->like('reference')->width(3);
                $filter->like('remark')->width(3);
                $filter->equal('status')->width(3);
                $filter->between('created_at')->datetime()->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    protected function form(): Form
    {
        return Form::make(MenuGroup::query(), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->text('title', __t('Title'))->required();
            $form->text('reference', __t('Reference'))
                ->required()
                ->help(__t('Unique identifier for the menu group (e.g., main-nav, footer-menu)'));
            $form->text('remark', __t('Remark'));
            $form->switch('status', __t('Status'))->default(1);
        });
    }
}
