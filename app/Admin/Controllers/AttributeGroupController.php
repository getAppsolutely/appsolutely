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
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('status')->switchable();

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
            $form->display('id');
            $form->text('title')->required();
            $form->multipleSelect('attributes', 'Attributes')
                ->options(Attribute::where('status', true)->pluck('title', 'id'))
                ->customFormat(extract_values());
            $form->switch('status')->default(true);
        });
    }
}
