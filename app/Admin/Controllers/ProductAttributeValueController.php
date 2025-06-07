<?php

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class ProductAttributeValueController extends AdminBaseController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(ProductAttributeValue::with(['attribute']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('attribute.title', __t('Attribute'));
            $grid->column('value', __t('Value'))->editable();
            $grid->column('slug', __t('Slug'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'value');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(4);
                $filter->like('value', __t('Value'))->width(4);
                $filter->like('slug', __t('Slug'))->width(4);
                $filter->equal('attribute_id', __t('Attribute'))->select(
                    ProductAttribute::where('status', true)->pluck('title', 'id')
                )->width(4);
                $filter->equal('status', __t('Status'))->select(Status::toArray())->width(4);
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
        return Form::make(new ProductAttributeValue(), function (Form $form) {
            $form->disableViewButton();
            $form->display('id', __t('ID'));
            $form->select('attribute_id', 'Attribute')
                ->options(ProductAttribute::where('status', true)->pluck('title', 'id'))
                ->required();
            $form->text('value', __t('Value'))->required();
            $form->text('slug', __t('Slug'));
            $form->switch('status', __t('Status'))->default(true);
        });
    }
}
