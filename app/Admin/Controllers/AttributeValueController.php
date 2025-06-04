<?php

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class AttributeValueController extends AdminBaseController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(AttributeValue::with(['attribute']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('attribute.title', 'Attribute');
            $grid->column('value')->editable();
            $grid->column('slug')->editable();
            $grid->column('status')->switch();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'value');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->like('value')->width(4);
                $filter->like('slug')->width(4);
                $filter->equal('attribute_id', 'Attribute')->select(
                    Attribute::where('status', true)->pluck('title', 'id')
                )->width(4);
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
        return Form::make(new AttributeValue(), function (Form $form) {
            $form->disableViewButton();
            $form->display('id');
            $form->select('attribute_id', 'Attribute')
                ->options(Attribute::where('status', true)->pluck('title', 'id'))
                ->required();
            $form->text('value')->required();
            $form->text('slug');
            $form->switch('status')->default(true);
        });
    }
}
