<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeleteAction;
use App\Admin\Forms\Models\PageBlockForm;
use App\Admin\Forms\Models\PageBlockGroupForm;
use App\Admin\Forms\Models\PageBlockSettingForm;
use App\Enums\BlockScope;
use App\Models\PageBlock;
use App\Models\PageBlockGroup;
use App\Models\PageBlockSetting;
use App\Repositories\PageBlockRepository;
use Arr;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;

final class PageBlockController extends AdminBaseController
{
    public function __construct(
        protected PageBlockRepository $blockRepository,
    ) {}

    public function index(Content $content): Content
    {
        return $content
            ->header(__t('Page Blocks'))
            ->description(__t('Manage Page Blocks'))
            ->body($this->buildTabs());
    }

    protected function buildTabs(): Tab
    {
        $tab = new Tab();

        $tab->add(__t('Block Settings'), $this->blockSettingsGrid(), true, 'block-settings');
        $tab->add(__t('Block'), $this->blocksGrid(), false, 'blocks');
        $tab->add(__t('Block Groups'), $this->blockGroupGrid(), false, 'block-groups');

        $tab->withCard();

        return $tab;
    }

    protected function blockSettingsGrid(): Grid
    {
        return Grid::make(PageBlockSetting::query()->with(['block', 'page']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('page.title', __t('Page'));
            $grid->column('block.title', __t('Block'));
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('sort', __t('Sort'))->editable();
            $grid->column('status', __t('Status'))->switch();

            // Filter to show only page-scoped blocks by default
            $grid->model()->whereHas('block', function ($query) {
                $query->where('scope', BlockScope::Page->value);
            });

            $grid->model()->orderByDesc('page_id');
            $grid->quickSearch('id', 'type', 'template');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->equal('page_id')->width(4);
                $filter->equal('block_id')->width(4);
                $filter->equal('type')->width(4);
                $filter->equal('status')->width(4);
                $filter->between('created_at')->datetime()->width(4);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Block Setting #' . $actions->getKey())
                    ->body(PageBlockSettingForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {});

            $grid->header(function () {
                return $this->renderGlobalBlocksQuickLinks();
            });
        });
    }

    protected function blocksGrid(): Grid
    {
        return Grid::make(PageBlock::query()->with('group'), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('group.title', __t('Group'));
            $grid->column('class', __t('Class'));
            $grid->column('droppable', __t('Droppable'))->switch();
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

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Block #' . $actions->getKey())
                    ->body(PageBlockForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Block'))
                        ->body(PageBlockForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function blockGroupGrid(): Grid
    {
        return Grid::make(PageBlockGroup::query(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
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

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Block Group #' . $actions->getKey())
                    ->body(PageBlockGroupForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Block Group'))
                        ->body(PageBlockGroupForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    public function renderGlobalBlocksQuickLinks(): string
    {
        $globalBlocks = $this->blockRepository->getGlobalBlocks();

        if ($globalBlocks->isEmpty()) {
            return '';
        }

        $modals = $globalBlocks->map(function ($block) {
            $blockSetting = Arr::first($block->settings);
            $buttonId     = 'global-block-btn-' . $blockSetting->id;

            $modal = Modal::make()
                ->xl()
                ->scrollable()
                ->title('Edit Global Block Setting: ' . $block->title . ' (Page: ' . $blockSetting->page->name . ')')
                ->body(PageBlockSettingForm::make($blockSetting->id)->payload([
                    'id' => $blockSetting->id,
                ]))
                ->button("<button type='button' class='btn btn-sm btn-primary mr-2 mt-1' id='{$buttonId}' style='text-decoration: none;'>
                    <i class='fa fa-globe mr-1'></i>{$block->title} ({$blockSetting->page->name})
                </button>");

            return $modal->render();
        })->join('');

        return "<div class='alert alert-info border-0 mt-1 w-100'>
            <div class='d-flex align-items-center mb-1'>
                <i class='fa fa-globe text-info mr-2'></i>
                <strong>Global Blocks Available</strong>
            </div>
            <p class='text-dark'>Click on any block below to quickly edit its global settings:</p>
            <div class='d-flex flex-wrap'>
                {$modals}
            </div>
        </div>";
    }
}
