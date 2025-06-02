<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\AppBuild;
use App\Models\AppVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class AppBuildController extends AdminBaseController
{
    public function grid(): Grid
    {
        return Grid::make(AppBuild::with('version'), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('version.version', 'Version');
            $grid->column('platform');
            $grid->column('arch');
            $grid->column('force_update')->bool();
            $grid->column('build_status');
            $grid->column('status')->switch();
            $grid->column('published_at')->sortable();
            $grid->column('created_at')->sortable();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('version_id')->select(AppVersion::pluck('version', 'id'));
                $filter->equal('platform')->select([
                    'windows' => 'Windows',
                    'darwin'  => 'Darwin',
                    'linux'   => 'Linux',
                ]);
                $filter->equal('status')->select([0 => 'Inactive', 1 => 'Active']);
            });
            $grid->model()->orderByDesc('id');
        });
    }

    public function form(): Form
    {
        return Form::make(AppBuild::with('version'), function (Form $form) {
            $form->display('id');
            $form->select('version_id')->options(AppVersion::pluck('version', 'id'))->required();
            $form->select('platform')->options([
                'windows' => 'Windows',
                'darwin'  => 'Darwin',
                'linux'   => 'Linux',
            ])->default('windows')->required();
            $form->text('arch');
            $form->switch('force_update');
            $form->json('gray_strategy');
            $form->textarea('release_notes');
            $form->text('build_status');
            $form->text('build_log');
            $form->text('signature');
            $form->switch('status');
            $form->datetime('published_at');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
