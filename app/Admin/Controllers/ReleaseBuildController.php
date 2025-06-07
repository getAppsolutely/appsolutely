<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\ReleaseBuild;
use App\Models\ReleaseVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class ReleaseBuildController extends AdminBaseController
{
    private const PLATFORMS = [
        'windows' => 'Windows',
        'darwin'  => 'Darwin',
        'linux'   => 'Linux',
        'ios'     => 'iOS',
        'android' => 'Android',
        'other'   => 'Other',
    ];

    private const ARCHS = [
        'x86_64'    => 'x86_64 (64-bit Intel/AMD)',
        'arm64'     => 'arm64 (Apple Silicon, ARM64)',
        'armv7'     => 'armv7 (32-bit ARM)',
        'ia32'      => 'ia32 (32-bit Intel/AMD)',
        'universal' => 'universal (macOS Universal)',
        'other'     => 'Other',
    ];

    public function grid(): Grid
    {
        return Grid::make(ReleaseBuild::with('version'), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('version.version', __t('Version'));
            $grid->column('platform', __t('Platform'));
            $grid->column('arch', __t('Arch'));
            $grid->column('force_update', __t('Force Update'))->bool();
            $grid->column('build_status', __t('Build Status'));
            $grid->column('status', __t('Status'))->switch();
            $grid->column('published_at', __t('Published At'))->sortable();
            $grid->column('created_at', __t('Created At'))->sortable();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'platform', 'arch');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('version_id')->select(ReleaseVersion::pluck('version', 'id'))->width(3);
                $filter->equal('platform')->select(self::PLATFORMS)->width(3);
                $filter->equal('status')->select(Status::toArray())->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    public function form(): Form
    {
        return Form::make(ReleaseBuild::with('version'), function (Form $form) {
            $form->display('id', __t('ID'));
            $versionOptions = ReleaseVersion::pluck('version', 'id')->toArray();

            $form->column(6, function (Form $form) use ($versionOptions) {
                $form->select('version_id', __t('Version'))
                    ->options($versionOptions)->required();

                $form->select('platform', __t('Platform'))->options(self::PLATFORMS)
                    ->default('windows')
                    ->help('Select a platform or enter a custom one below.')
                    ->when('other', function (Form $form) {
                        $form->text('_platform', __t('Custom Platform (optional)'))
                            ->help('If filled, this will override the selected platform.');
                    });
                $form->select('arch', __t('Arch'))->options(self::ARCHS)
                    ->help('Select the architecture or enter a custom one below.')
                    ->when('other', function (Form $form) {
                        $form->text('_arch', __t('Custom Arch (optional)'))
                            ->help('If filled, this will override the selected architecture.');
                    });
                $form->textarea('release_notes', __t('Release Notes'));
                $form->keyValue('gray_strategy', __t('Gray Strategy'))
                    ->default([])
                    ->setKeyLabel('Key')
                    ->setValueLabel('Value')
                    ->saveAsJson()
                    ->help('Key-value pairs will be saved as JSON. Example: Key: percent   Value: 20 | Key: uuid_hash_range   Value: [0, 2000]');

            });

            $form->column(6, function (Form $form) {
                $form->text('build_status', __t('Build Status'));
                $form->text('build_log', __t('Build Log'));
                $form->file('path', __t('Build File'))
                    ->autoUpload()
                    ->url(upload_to_api(ReleaseBuild::class, $form->getKey()))
                    ->uniqueName()
                    ->help('Upload a build file. The path will be stored and used for download. You can also enter a path manually.');

                $form->text('signature', __t('Signature'));

                $form->switch('force_update', __t('Force Update'));
                $form->switch('status', __t('Status'));
                $form->datetime('published_at', __t('Published At'));
            });

            $form->disableViewButton();
            $form->disableViewCheck();

            $form->saving(function (Form $form) {
                if (! empty($form->custom_platform)) {
                    $form->platform = $form->_custom_platform;
                }
                if (! empty($form->custom_arch)) {
                    $form->arch = $form->_custom_arch;
                }
            });
            $form->ignore(['_platform', '_arch']);
        });
    }
}
