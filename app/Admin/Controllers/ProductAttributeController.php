<?php

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeGroup;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class ProductAttributeController extends AdminBaseController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ProductAttribute(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('slug', __t('Slug'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(4);
                $filter->like('title', __t('Title'))->width(4);
                $filter->like('slug', __t('Slug'))->width(4);
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
        return Form::make(ProductAttribute::with(['attributeGroups']), function (Form $form) {
            $form->disableViewButton();
            $form->display('id', __t('ID'));

            $form->text('title', __t('Title'))->required();
            $form->text('slug', __t('Slug'))->help(__t('Leave empty to auto-generate from title'));
            $form->text('remark', __t('Remark'));
            $form->switch('status', __t('Status'))->default(true);

            $form->multipleSelect('attributeGroups', __t('Attribute Groups'))
                ->options(ProductAttributeGroup::where('status', true)->pluck('title', 'id'))
                ->customFormat(extract_values());
        });
    }
}
