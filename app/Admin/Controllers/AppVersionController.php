<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\AppVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class AppVersionController extends AdminBaseController
{
    public function grid(): Grid
    {
        return Grid::make(AppVersion::query(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('version')->editable();
            $grid->column('remark')->editable();
            $grid->column('release_channel')->editable();
            $grid->column('status')->switch();
            $grid->column('published_at')->editable()->sortable();
            $grid->column('created_at')->display(column_time_format())->sortable();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();
            });
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'version', 'remark');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('version')->width(3);
                $filter->equal('platform')->width(3);
                $filter->equal('status')->select(Status::toArray())->width(3);
            });
        });
    }

    public function form(): Form
    {
        return Form::make(AppVersion::query(), function (Form $form) {
            $form->display('id');
            $form->text('version')->required();
            $form->text('remarks');
            $form->text('release_channel');
            $form->switch('status');
            $form->datetime('published_at');
            $form->display('created_at');
            $form->display('updated_at');
            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
