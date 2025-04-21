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
            $grid->column('type');
            $grid->column('status')->switch();
            $grid->column('title');
            $grid->column('categories')->pluck('title')->label();
            $grid->column('skus')->display(function ($skus) {
                return count($skus);
            });

            $grid->column('published_at')->display(function ($timestamp) {
                return TimeHelper::format($timestamp);
            })->sortable();

            $grid->column('expired_at')->display(function ($timestamp) {
                return TimeHelper::format($timestamp);
            })->sortable();

            $grid->column('sort')->editable();

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
                ->add('Basic', $this->basicForm()->edit($id))
                ->add('SKUs', $this->skusGrid($id))
                ->add('Optional', $this->optionalForm()->edit($id))
            );
    }

    protected function basicForm(): Form
    {
        return Form::make(Product::with(['categories']), function (Form $form) {
            $form->display('id');
            $form->select('type')->options(Product::getProductTypes())->required();

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
        });
    }

    protected function optionalForm(): Form
    {
        return Form::make(Product::with(['categories']), function (Form $form) {
            $form->image('cover')->autoUpload()->url(upload_url(Product::class, $form->getKey()));
            $form->textarea('keywords')->rows(2);
            $form->textarea('description')->rows(3);

            $form->keyValue('type_config')
                ->setKeyLabel('Key')
                ->setValueLabel('Value')
                ->saveAsJson();

            $form->keyValue('setting')
                ->setKeyLabel('Key')
                ->setValueLabel('Value')
                ->saveAsJson();

            $form->keyValue('payments')
                ->setKeyLabel('Key')
                ->setValueLabel('Value')
                ->saveAsJson();

            $form->keyValue('form_columns')
                ->setKeyLabel('Key')
                ->setValueLabel('Value')
                ->saveAsJson();

            $form->display('created_at');
            $form->display('updated_at');
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
            $grid->column('stock')->editable();
            $grid->column('original_price')->editable();
            $grid->column('price')->editable();
            $grid->column('status')->switch();
            $grid->column('sort')->editable()->sortable();

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
