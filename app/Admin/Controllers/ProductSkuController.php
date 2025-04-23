<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Fields\Markdown;
use App\Admin\Forms\ProductSkuForm;
use App\Enums\Status;
use App\Models\Product;
use App\Models\ProductSku;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSkuRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Modal;

class ProductSkuController extends AdminBaseController
{
    public function __construct(protected ProductRepository $productRepository,
        protected ProductSkuRepository $productSkuRepository) {}

    /**
     * Make a grid builder.
     */
    protected function grid(): Grid
    {
        return Grid::make(ProductSku::with(['product']), function (Grid $grid) {

            $grid->column('id')->sortable();
            $grid->column('product.title', 'Product');
            $grid->column('title')->editable();
            $grid->column('slug')->editable();
            $grid->column('attributes')->display(function ($attributes) {
                if (is_array($attributes) && ! empty($attributes)) {
                    return collect($attributes)->map(function ($value, $key) {
                        return "<span class='badge badge-info mr-1'>$key: $value</span>";
                    })->implode(' ');
                }

                return '';
            });
            $grid->column('stock')->editable();
            $grid->column('original_price')->editable();
            $grid->column('price')->editable();
            $grid->column('sort')->editable()->sortable();
            $grid->column('status')->switch();
            $grid->column('published_at')->sortable();
            $grid->column('expired_at')->sortable();

            $grid->model()->orderBy('id', 'desc');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $products = $this->productRepository->getActiveList();
                $filter->equal('product_id', 'Product')->select($products)->width(3);
                $filter->like('title', 'Title')->width(3);
                $filter->equal('status')->select(Status::toArray())->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();

                // Add custom edit button that opens in modal
                $editModal = Modal::make()->xl()->scrollable()
                    ->title('Edit SKU')
                    ->body(ProductSkuForm::make()->payload([
                        'product_id' => $actions->row->product_id,
                        'id'         => $actions->row->id,
                    ]))
                    ->button(edit_action());
                $actions->prepend($editModal);
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
        return Form::make(new ProductSku(), function (Form $form) {
            $form->display('id');

            // Product selection
            $products = $this->productRepository->getActiveList();
            $form->select('product_id', 'Product')
                ->options($products)
                ->required();

            $form->text('title')->required();
            $form->text('slug')->help(__t('Leave empty to auto-generate from title'));

            $form->keyValue('attributes')
                ->setKeyLabel('Attribute')
                ->setValueLabel('Value')
                ->saveAsJson();

            $form->image('cover')->autoUpload()->url(upload_url(ProductSku::class, $form->getKey()));
            $form->textarea('keywords')->rows(2);
            $form->textarea('description')->rows(3);
            $form->markdown('content')->options(Markdown::options())->script(Markdown::script());

            $form->currency('original_price')->symbol(app_currency_symbol());
            $form->currency('price')->symbol(app_currency_symbol())->required();
            $form->number('stock')->default(0)->min(0);
            $form->number('sort')->default(99);
            $form->switch('status')->default(1);

            $form->datetime('published_at');
            $form->datetime('expired_at');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
