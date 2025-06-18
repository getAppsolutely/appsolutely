<?php

namespace App\Admin\Controllers;

use App\Enums\MenuTarget;
use App\Enums\MenuType;
use App\Models\Menu;
use App\Models\MenuGroup;
use App\Repositories\MenuRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Illuminate\Support\Facades\Request;

final class MenuController extends AdminBaseController
{
    public function __construct(protected MenuRepository $menuRepository) {}

    protected function grid(): Grid
    {
        return Grid::make(Menu::with(['menuGroup']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->width('50px');
            $grid->column('title', __t('Title'))->tree(true)->width('300px');
            $grid->column('menuGroup.title', __t('Menu Group'));
            $grid->column('children', __t('Children'))->display(function () {
                return $this->children()->count();
            })->width('80px')->setAttributes(children_attributes());
            $grid->column('type', __t('Type'))->display(function ($type) {
                return $type->toArray();
            });
            $grid->column('route', __t('Route'))->width('200px');
            $grid->column('target', __t('Target'))->display(function ($target) {
                return $target->toArray();
            });
            $grid->column('is_external', __t('External'))->bool();
            $grid->column('published_at', __t('Published At'))->display(column_time_format())->sortable();
            $grid->column('expired_at', __t('Expired At'))->display(column_time_format())->sortable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderBy('left', 'ASC');

            $grid->quickSearch('id', 'title', 'route');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('title')->width(3);
                $filter->equal('menu_group_id', __t('Menu Group'))->select(MenuGroup::pluck('title', 'id'))->width(3);
                $filter->equal('type', __t('Type'))->select(enum_options(MenuType::class))->width(3);
                $filter->equal('status')->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    protected function form(): Form
    {
        return Form::make(Menu::with(['menuGroup']), function (Form $form) {
            $form->display('id', __t('ID'));

            $form->select('menu_group_id', __t('Menu Group'))
                ->options(MenuGroup::pluck('title', 'id'))
                ->required();

            $form->select('parent_id', __t('Parent Menu'))
                ->options(Menu::pluck('title', 'id'))
                ->help(__t('Leave empty for root menu'));

            $form->text('title', __t('Title'))->required();
            $form->text('remark', __t('Remark'));
            $form->text('route', __t('Route'));

            $form->radio('type', __t('Type'))
                ->options(enum_options(MenuType::class))
                ->default(MenuType::Link->value)
                ->required();

            $form->text('icon', __t('Icon'))->help(__t('CSS class or icon name'));
            $form->text('permission_key', __t('Permission Key'));

            $form->radio('target', __t('Target'))
                ->options(enum_options(MenuTarget::class))
                ->default(MenuTarget::Self->value);

            $form->switch('is_external', __t('External Link'))->default(false);

            $form->datetime('published_at', __t('Published At (%s)', [app_local_timezone()]));
            $form->datetime('expired_at', __t('Expired At (%s)', [app_local_timezone()]));

            $form->switch('status', __t('Status'))->default(1);

            $form->saving(function (Form $form) {
                /** @var Menu $model */
                $model = $form->model();

                if (Request::has('parent_id')) {
                    $parentId = $form->input('parent_id');
                    if ($parentId) {
                        $parent = $model->find($parentId);
                        if ($parent) {
                            $model->appendToNode($parent)->save();
                        } else {
                            $model->saveAsRoot();
                        }
                    } else {
                        $model->saveAsRoot();
                    }
                } elseif (Request::has('_orderable')) {
                    $moveUp = $form->input('_orderable') == 1;
                    $node   = $model->find($form->getKey());
                    if ($moveUp) {
                        $node->up();
                    } else {
                        $node->down();
                    }
                } else {
                    $model->save();
                }

                return $model;
            });
        });
    }
}
