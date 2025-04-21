<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Fields\Markdown;
use App\Models\Product;
use App\Models\ProductSku;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Illuminate\Http\Request;

class ProductSkuController extends AdminBaseController
{
    public function index(Content $content)
    {
        if (request('product_id')) {
            return $content->body($this->grid());
        }

        return admin_redirect(admin_route('products.edit', ['product' => session('previous.product_id')]));
    }

    protected function grid(): Grid
    {
        $productId = request('product_id');
        $product   = Product::findOrFail($productId);

        return Grid::make(ProductSku::with(['product']), function (Grid $grid) use ($product) {
            $grid->model()->where('product_id', $product->id);

            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('stock');
            $grid->column('original_price');
            $grid->column('price');
            $grid->column('status')->switch();
            $grid->column('sort')->editable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('title');
            });

            // Add create button that redirects to create form with product_id
            $grid->tools(function (Grid\Tools $tools) use ($product) {
                $tools->append('<a class="btn btn-primary" href="' .
                    admin_url('product-skus/create?product_id=' . $product->id) . '">
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

    protected function form(): Form
    {
        // dd(request()->route()->getName());
        return Form::make(new ProductSku(), function (Form $form) {
            // If creating new SKU, get product_id from query string
            if ($form->isCreating()) {
                $productId = request('product_id');
                if (! $productId) {
                    return redirect()->route('admin.products.index');
                }
                $form->hidden('product_id')->value($productId);
            }

            $form->display('id');
            if ($form->isEditing()) {
                $form->display('product.title', 'Product');
            }

            $form->text('title')->required();
            $form->text('slug');
            $form->image('cover')->autoUpload()->url(upload_url(ProductSku::class, $form->getKey()));
            $form->textarea('keywords')->rows(2);
            $form->textarea('description')->rows(3);
            $form->markdown('content')->options(Markdown::options())->script(Markdown::script());

            $form->number('stock')->default(0);
            $form->number('original_price');
            $form->number('price')->required();
            $form->number('sort');
            $form->switch('status');

            $form->display('created_at');
            $form->display('updated_at');

            // After saved, redirect back to product SKUs list
            $form->saved(function (Form $form) {
                $productId = $form->model()->product_id ?? request('product_id');
                session()->flash('previous.product_id', $productId);
            });
        });
    }
}
