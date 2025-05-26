<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeleteAction;
use App\Admin\Forms\Fields\Markdown;
use App\Admin\Forms\ProductSkuForm;
use App\Admin\Forms\ProductSkuGeneratorForm;
use App\Models\Product;
use App\Models\ProductSku;
use App\Repositories\ProductCategoryRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Modal;

class ProductController extends AdminBaseController
{
    protected ProductCategoryRepository $productCategoryRepository;

    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    protected function grid(): Grid
    {
        return Grid::make(Product::with(['categories', 'skus']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('categories')->pluck('title')->label();
            $grid->column('skus')->display(column_count());

            $grid->column('type')->display(function ($type) {
                return Product::getProductTypes()[$type] ?? $type;
            })->label();

            $grid->column('published_at_local')->sortable();
            $grid->column('expired_at_local')->sortable();

            $grid->column('sort')->quickEdit();
            $grid->column('status')->switchable();

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('title')->width(3);
                $filter->equal('type')->select(Product::getProductTypes())->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });

            $grid->model()->orderBy('id', 'desc');
        });
    }

    protected function form(): Form
    {
        return Form::make(Product::with(['categories']), function (Form $form) {
            $form->defaultEditingChecked();

            $form->tab('Basic', function (Form $form) {
                $this->basicForm($form);
            }, true, 'basic')->tab('SKUs', function (Form $form) {
                $form->html($this->skusGrid($form->getKey()));
            }, false, 'skus')->tab('Optional', function (Form $form) {
                $this->optionalForm($form);
            }, false, 'optional');
        });
    }

    protected function basicForm(Form $form): Form
    {
        $form->display('id');
        $form->radio('type')->options(Product::getProductTypes())
            ->default(Product::TYPE_PHYSICAL_PRODUCT)
            ->when(Product::TYPE_PHYSICAL_PRODUCT, function (Form $form) {
                $shipmentMethods = associative_array(Product::getShipmentMethodForPhysicalProduct());
                $form->multipleSelect('shipment_methods')
                    ->options($shipmentMethods)
                    ->default(array_shift($shipmentMethods));
            })
            ->when(Product::TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT, function (Form $form) {
                $shipmentMethods = associative_array(Product::getShipmentMethodForAutoVirtualProduct());
                $form->multipleSelect('shipment_methods')
                    ->options($shipmentMethods)
                    ->default(array_shift($shipmentMethods));
            })
            ->when(Product::TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT, function (Form $form) {
                $shipmentMethods = associative_array(Product::getShipmentMethodForManualVirtualProduct());
                $form->multipleSelect('shipment_methods')
                    ->options($shipmentMethods)
                    ->default(array_shift($shipmentMethods));
            })->required();

        $availableCategories = $this->productCategoryRepository->getActiveList();
        $form->multipleSelect('categories', 'Categories')
            ->options($availableCategories)
            ->customFormat(extract_values());

        $form->text('title')->required();
        $form->text('subtitle');
        $form->text('slug');
        $form->markdown('content')->options(Markdown::options())->script(Markdown::script());

        $form->currency('original_price')->symbol(app_currency_symbol())->default(999);
        $form->currency('price')->symbol(app_currency_symbol())->default(999);

        $form->datetime('published_at_local');
        $form->datetime('expired_at_local');
        $form->switch('status');

        $form->disableViewButton();
        $form->disableViewCheck();

        return $form;
    }

    protected function optionalForm(Form $form): Form
    {
        $form->image('cover')->autoUpload()->url(upload_url(Product::class, $form->getKey()));
        $form->textarea('keywords')->rows(2);
        $form->textarea('description')->rows(3);

        $form->keyValue('setting')->default([])
            ->setKeyLabel('Key')
            ->setValueLabel('Value')
            ->saveAsJson();

        $form->multipleSelect('payment_methods');
        // ->options($this->paymentRepository->list()->pluck('title', 'id'));

        $form->table('additional_columns', function (Form\NestedForm $table) {
            $table->text('field');
            $table->text('name');
            $table->select('input_type')->options([
                'input' => 'Input',
                'text'  => 'Text',
            ]);
            $table->switch('required')->default(true);
        })->saving(function ($value) {
            return json_encode($value);
        });

        $form->display('created_at_local');
        $form->display('updated_at_local');

        $form->disableViewButton();
        $form->disableViewCheck();

        return $form;
    }

    protected function skusGrid($productId): Grid
    {
        return Grid::make(ProductSku::with(['product']), function (Grid $grid) use ($productId) {
            $grid->model()->where('product_id', $productId);

            $grid->column('id')->sortable();
            $grid->column('attributes')->display(column_value_simple('value', 'data'));
            $grid->column('title')->quickEdit();
            $grid->column('slug')->quickEdit();
            $grid->column('original_price')->quickEdit();
            $grid->column('price')->quickEdit();
            $grid->column('stock')->quickEdit();
            $grid->column('status')->switchable();
            $grid->column('sort')->sortable()->quickEdit();

            // Add create button that opens the modal form
            $grid->tools(function (Grid\Tools $tools) use ($productId) {
                $modal = Modal::make()
                    ->lg()
                    ->title('Create SKUs')
                    ->body(ProductSkuGeneratorForm::make()->payload(['product_id' => $productId]))
                    ->button(admin_create_button());

                $tools->append($modal);
            });

            $grid->disableCreateButton();

            // Show row actions with edit in modal
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();
                $actions->disableDelete();

                // Add custom edit button that opens in modal
                $editModal = Modal::make()->xl()->scrollable()
                    ->title('Edit SKU')
                    ->body(ProductSkuForm::make()->payload([
                        'product_id' => $actions->row->product_id,
                        'id'         => $actions->row->id,
                    ]))
                    ->button(admin_edit_action());
                $actions->append($editModal);
                $actions->append(new DeleteAction());
            });

            $grid->setResource(admin_route('product.skus.index'));
        });
    }
}
