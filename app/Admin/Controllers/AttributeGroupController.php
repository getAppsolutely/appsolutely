<?php

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class AttributeGroupController extends AdminBaseController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new AttributeGroup(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->like('title')->width(4);
                $filter->equal('status')->select(Status::toArray())->width(4);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(AttributeGroup::with(['attributes']), function (Form $form) {
            $form->disableViewButton();
            $form->display('id', __t('ID'));
            $form->text('title', __t('Title'))->required();
            $form->text('remark', __t('Remark'));
            $form->multipleSelect('attributes', 'Attributes')
                ->options(Attribute::where('status', true)->pluck('title', 'id'))
                ->customFormat(extract_values());
            $form->switch('status', __t('Status'))->default(true);
        });
    }
}
