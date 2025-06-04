<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\ProductSkuForm;
use App\Enums\Status;
use App\Models\ProductSku;
use App\Repositories\ProductRepository;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Modal;

class ProductSkuController extends AdminBaseController
{
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Make a grid builder.
     */
    protected function grid(): Grid
    {
        return Grid::make(ProductSku::with(['product']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('product.title', 'Product');
            $grid->column('attributes')->display(column_value_simple('value', 'data'));
            $grid->column('title')->editable();
            $grid->column('slug')->editable();
            $grid->column('stock')->editable();
            $grid->column('original_price')->editable();
            $grid->column('price')->editable();
            $grid->column('sort')->editable()->sortable();
            $grid->column('status')->switch();
            $grid->column('published_at')->display(column_time_format())->sortable();
            $grid->column('expired_at')->display(column_time_format())->sortable();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $products = $this->productRepository->getActiveList();
                $filter->equal('product_id', 'Product')->select($products)->width(3);
                $filter->like('title', 'Title')->width(3);
                $filter->equal('status')->select(Status::toArray())->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableEdit();

                // Add the custom edit button that opens in modal
                $editModal = Modal::make()->xl()->scrollable()
                    ->title('Edit SKU')
                    ->body(ProductSkuForm::make()->payload([
                        'product_id' => $actions->row->product_id,
                        'id'         => $actions->row->id,
                    ]))
                    ->button(admin_edit_action());
                $actions->prepend($editModal);
            });
        });
    }
}
