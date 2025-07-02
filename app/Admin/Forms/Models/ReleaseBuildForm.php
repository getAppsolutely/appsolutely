<?php

namespace App\Admin\Forms\Models;

use App\Enums\Architecture;
use App\Enums\Platform;
use App\Models\ReleaseBuild;
use App\Models\ReleaseVersion;
use Dcat\Admin\Widgets\Form;

class ReleaseBuildForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new ReleaseBuild();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        $versionOptions = ReleaseVersion::pluck('version', 'id')->toArray();

        $this->column(6, function (Form $form) use ($versionOptions) {
            $form->select('version_id', __t('Version'))
                ->options($versionOptions)->required();

            $form->select('platform', __t('Platform'))->options(Platform::toArray())
                ->default('windows')
                ->help('Select a platform or enter a custom one below.')
                ->when('other', function (Form $form) {
                    $form->text('_platform', __t('Custom Platform (optional)'))
                        ->help('If filled, this will override the selected platform.');
                });
            $form->select('arch', __t('Arch'))->options(Architecture::toArray())
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

        $this->column(6, function (Form $form) {
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
    }
}
