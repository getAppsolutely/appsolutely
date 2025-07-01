<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\ReleaseVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class ReleaseVersionController extends AdminBaseController
{
    public function grid(): Grid
    {
        return Grid::make(ReleaseVersion::query(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('version', __t('Version'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('release_channel', __t('Release Channel'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->column('published_at', __t('Published At'))->editable()->sortable();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();
            });
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'version', 'remark');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('version')->width(3);
                $filter->equal('platform')->width(3);
                $filter->equal('status', __t('Status'))->select(Status::toArray())->width(3);
            });
        });
    }

    public function form(): Form
    {
        return Form::make(ReleaseVersion::query(), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->text('version', __t('Version'))->required();
            $form->text('remark', __t('Remark'));
            $form->text('release_channel', __t('Release Channel'));
            $form->switch('status', __t('Status'));
            $form->datetime('published_at', __t('Published At'));
            $form->display('created_at', __t('Created At'));
            $form->display('updated_at', __t('Updated At'));
            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
