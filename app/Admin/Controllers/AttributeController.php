<?php

namespace App\Admin\Controllers;

use App\Enums\Status;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class AttributeController extends AdminBaseController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Attribute(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('slug', __t('Slug'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->like('title')->width(4);
                $filter->like('slug')->width(4);
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
        return Form::make(Attribute::with(['attributeGroups']), function (Form $form) {
            $form->disableViewButton();
            $form->display('id', __t('ID'));

            $form->text('title', __t('Title'))->required();
            $form->text('slug', __t('Slug'))->help(__t('Leave empty to auto-generate from title'));
            $form->text('remark', __t('Remark'));
            $form->switch('status', __t('Status'))->default(true);

            $form->multipleSelect('attributeGroups', 'Attribute Groups')
                ->options(AttributeGroup::where('status', true)->pluck('title', 'id'))
                ->customFormat(extract_values());
        });
    }
}
