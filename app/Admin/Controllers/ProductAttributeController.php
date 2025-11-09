<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeleteAction;
use App\Admin\Forms\Models\ProductAttributeForm;
use App\Admin\Forms\Models\ProductAttributeGroupForm;
use App\Admin\Forms\Models\ProductAttributeValueForm;
use App\Enums\Status;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeGroup;
use App\Models\ProductAttributeValue;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;

final class ProductAttributeController extends AdminBaseController
{
    public function index(Content $content): Content
    {
        return $content
            ->header(__t('Product Attributes'))
            ->description(__t('Manage Product Attributes'))
            ->body($this->buildTabs());
    }

    protected function buildTabs(): Tab
    {
        $tab = new Tab();

        $tab->add(__t('Attributes'), $this->attributesGrid(), true, 'attributes');
        $tab->add(__t('Attribute Values'), $this->attributeValuesGrid(), false, 'attribute-values');
        $tab->add(__t('Attribute Groups'), $this->attributeGroupsGrid(), false, 'attribute-groups');

        $tab->withCard();

        return $tab;
    }

    protected function attributesGrid(): Grid
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

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Attribute #' . $actions->getKey())
                    ->body(ProductAttributeForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Attribute'))
                        ->body(ProductAttributeForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function attributeValuesGrid(): Grid
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
                    ProductAttribute::status()->pluck('title', 'id')
                )->width(4);
                $filter->equal('status', __t('Status'))->select(Status::toArray())->width(4);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Attribute Value #' . $actions->getKey())
                    ->body(ProductAttributeValueForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Attribute Value'))
                        ->body(ProductAttributeValueForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function attributeGroupsGrid(): Grid
    {
        return Grid::make(new ProductAttributeGroup(), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('remark', __t('Remark'))->editable();
            $grid->column('status', __t('Status'))->switch();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(4);
                $filter->like('title', __t('Title'))->width(4);
                $filter->equal('status', __t('Status'))->select(Status::toArray())->width(4);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Attribute Group #' . $actions->getKey())
                    ->body(ProductAttributeGroupForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Attribute Group'))
                        ->body(ProductAttributeGroupForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }
}
