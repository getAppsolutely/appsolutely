<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Fields\Markdown;
use App\Helpers\TimeHelper;
use App\Models\Product;
use App\Models\ProductSku;
use App\Repositories\ProductCategoryRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;

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
            $grid->column('skus')->display(function ($skus) {
                return count($skus);
            });

            $grid->column('type')->display(function ($type) {
                return Product::getProductTypes()[$type] ?? $type;
            })->label();

            $grid->column('published_at')->display(function ($timestamp) {
                return TimeHelper::format($timestamp);
            })->sortable();

            $grid->column('expired_at')->display(function ($timestamp) {
                return TimeHelper::format($timestamp);
            })->sortable();

            $grid->column('sort')->editable();
            $grid->column('status')->switch();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('title');
                $filter->equal('type')->select(Product::getProductTypes());
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    public function edit($id, Content $content): Content
    {
        return $content->header('Edit Product')
            ->description('Edit product and manage SKUs')
            ->body(Tab::make()
                ->add('Basic', $this->basicForm()->edit($id), true, 'basic')
                ->add('SKUs', $this->skusGrid($id), false, 'sku')
                ->add('Optional', $this->optionalForm()->edit($id), false, 'optional')
            );
    }

    protected function basicForm(): Form
    {
        return Form::make(Product::with(['categories']), function (Form $form) {
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
                ->customFormat(function ($v) {
                    if (! $v) {
                        return [];
                    }

                    return array_column($v, 'id');
                });

            $form->text('title')->required();
            $form->text('slug');
            $form->markdown('content')->options(Markdown::options())->script(Markdown::script());

            $form->datetime('published_at');
            $form->datetime('expired_at');
            $form->switch('status');

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }

    protected function optionalForm(): Form
    {
        return Form::make(Product::with(['categories']), function (Form $form) {
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

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }

    protected function form(): Form
    {
        return $this->basicForm();
    }

    protected function skusGrid($productId): Grid
    {
        return Grid::make(ProductSku::with(['product']), function (Grid $grid) use ($productId) {
            $grid->model()->where('product_id', $productId);

            $grid->column('id')->sortable();
            $grid->column('title')->editable();
            $grid->column('slug')->editable();
            $grid->column('original_price')->editable();
            $grid->column('price')->editable();
            $grid->column('stock')->editable();
            $grid->column('status')->switch();
            $grid->column('sort')->editable()->sortable()->orderable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('title')->width(3);
            });

            // Add create button that redirects to create form with product_id
            $grid->tools(function (Grid\Tools $tools) use ($productId) {
                $tools->append('<a class="btn btn-primary" href="' .
                    admin_url('product-skus/create?product_id=' . $productId) . '">
                    <i class="feather icon-plus"></i> Create</a>');
            });

            // Always show quick create button
            $grid->disableCreateButton();

            // Show row actions
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });

            // Set resource url to include product_id
            $grid->setResource('product-skus');
        });
    }
}
