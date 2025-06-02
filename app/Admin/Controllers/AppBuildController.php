<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\AppBuild;
use App\Models\AppVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

final class AppBuildController extends AdminBaseController
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
        return Grid::make(AppBuild::with('version'), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('version.version', 'Version');
            $grid->column('platform');
            $grid->column('arch');
            $grid->column('force_update')->bool();
            $grid->column('build_status');
            $grid->column('status')->switch();
            $grid->column('published_at_local')->sortable();
            $grid->column('created_at_local')->sortable();
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
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    public function form(): Form
    {
        return Form::make(AppBuild::with('version'), function (Form $form) {
            $form->display('id');

            $versionOptions = AppVersion::pluck('version', 'id')->toArray();
            $hasVersions    = count($versionOptions) > 0;

            $form->column(6, function (Form $form) use ($hasVersions, $versionOptions) {
                if ($hasVersions) {
                    $form->select('version_id', 'Version')
                        ->options($versionOptions + ['__new__' => '➕ Add new version'])
                        ->when('__new__', function (Form $form) {
                            $form->text('new_version_name', 'New Version Name');
                        })
                        ->required();
                } else {
                    $form->text('new_version_name', 'Version Name')->required();
                }
                $form->select('platform')->options(self::PLATFORMS)
                    ->default('windows')
                    ->help('Select a platform or enter a custom one below.')
                    ->when('other', function (Form $form) {
                        $form->text('custom_platform', 'Custom Platform (optional)')
                            ->help('If filled, this will override the selected platform.');
                    });
                $form->select('arch')->options(self::ARCHS)
                    ->help('Select the architecture or enter a custom one below.')
                    ->when('other', function (Form $form) {
                        $form->text('custom_arch', 'Custom Arch (optional)')
                            ->help('If filled, this will override the selected architecture.');
                    });
                $form->textarea('release_notes');
                $form->text('build_status');
                $form->text('build_log');
                $form->file('path', 'Build File')
                    ->autoUpload()
                    ->url(upload_url(AppBuild::class, $form->getKey()))
                    ->uniqueName()
                    ->help('Upload a build file. The path will be stored and used for download. You can also enter a path manually.');
            });

            $form->column(6, function (Form $form) {
                $form->text('signature');
                $form->keyValue('gray_strategy')
                    ->default([])
                    ->setKeyLabel('Key')
                    ->setValueLabel('Value')
                    ->saveAsJson()
                    ->help('Key-value pairs will be saved as JSON. Example: Key: percent   Value: 20 | Key: uuid_hash_range   Value: [0, 2000]');

                $form->switch('force_update');
                $form->switch('status');
                $form->datetime('published_at_local');
                $form->display('created_at_local');
            });

            $form->disableViewButton();
            $form->disableViewCheck();

            $form->saving(function (Form $form) use ($hasVersions) {
                $newVersionName = $form->new_version_name;
                if ($hasVersions && $form->version_id === '__new__') {
                    if (! $newVersionName) {
                        return $form->response()->error('Please enter a version name.');
                    }
                }
                if ((! $hasVersions && $newVersionName) || ($hasVersions && $form->version_id === '__new__' && $newVersionName)) {
                    $existing = AppVersion::where('version', $newVersionName)->first();
                    if ($existing) {
                        return $form->response()->error('Version already exists. Please select it from the list.');
                    }
                    $version = AppVersion::create([
                        'version'      => $newVersionName,
                        'status'       => 1,
                        'published_at' => now(),
                    ]);
                    $form->version_id = $version->id;
                }
                if (! empty($form->custom_platform)) {
                    $form->platform = $form->custom_platform;
                }
                if (! empty($form->custom_arch)) {
                    $form->arch = $form->custom_arch;
                }
                unset($form->new_version_name, $form->custom_platform, $form->custom_arch);
            });
            $form->ignore(['new_version_name', 'custom_platform', 'custom_arch']);
        });
    }
}
