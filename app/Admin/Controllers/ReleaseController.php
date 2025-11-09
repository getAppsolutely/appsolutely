<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeleteAction;
use App\Admin\Forms\Models\ReleaseBuildForm;
use App\Admin\Forms\Models\ReleaseVersionForm;
use App\Enums\Platform;
use App\Enums\Status;
use App\Models\ReleaseBuild;
use App\Models\ReleaseVersion;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;

final class ReleaseController extends AdminBaseController
{
    public function index(Content $content): Content
    {
        return $content
            ->header(__t('Release'))
            ->description(__t('Manage releases'))
            ->body($this->buildTabs());
    }

    protected function buildTabs(): Tab
    {
        $tab = new Tab();

        $tab->add(__t('Builds'), $this->buildsGrid(), true, 'builds');
        $tab->add(__t('Versions'), $this->versionsGrid(), false, 'versions');

        $tab->withCard();

        return $tab;
    }

    protected function buildsGrid(): Grid
    {
        return Grid::make(ReleaseBuild::with('version'), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('version.version', __t('Version'));
            $grid->column('platform', __t('Platform'));
            $grid->column('arch', __t('Arch'));
            $grid->column('force_update', __t('Force Update'))->bool();
            $grid->column('build_status', __t('Build Status'));
            $grid->column('status', __t('Status'))->switch();
            $grid->column('published_at', __t('Published At'))->display(column_time_format())->sortable();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'platform', 'arch');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('version_id')->select(ReleaseVersion::pluck('version', 'id'))->width(3);
                $filter->equal('platform')->select(Platform::toArray())->width(3);
                $filter->equal('status')->select(Status::toArray())->width(3);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Version #' . $actions->getKey())
                    ->body(ReleaseBuildForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Version'))
                        ->body(ReleaseBuildForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function versionsGrid(): Grid
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

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Block #' . $actions->getKey())
                    ->body(ReleaseVersionForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Block'))
                        ->body(ReleaseVersionForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }
}
