<?php

namespace App\Admin\Forms;

use App\Admin\Forms\Fields\Markdown;
use App\Models\ProductSku;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class ProductSkuForm extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        try {
            $id = $this->payload['id'] ?? null;

            if ($id) {
                $sku = ProductSku::findOrFail($id);
                $sku->update($input);

                return $this->response()->success(__t('SKU updated successfully'))->refresh();
            } else {
                ProductSku::create($input);

                return $this->response()->success(__t('SKU created successfully'))->refresh();
            }
        } catch (\Exception $e) {
            return $this->response()->error(__t('Failed to save SKU: ') . $e->getMessage());
        }
    }

    public function form()
    {
        // Get data from payload
        $id        = $this->payload['id'] ?? null;
        $productId = $this->payload['product_id'] ?? null;

        if ($id) {
            $sku = ProductSku::with(['product'])->findOrFail($id);
            $this->fill($sku);

            $this->display('id');
            $this->display('product.title', 'Product');
        }

        $this->text('title')->required();
        $this->text('slug')->help(__t('Leave empty to auto-generate from title'));
        $this->image('cover')->autoUpload()->url(upload_url(ProductSku::class, $id));
        $this->textarea('keywords')->rows(2);
        $this->textarea('description')->rows(3);
        $this->markdown('content')->options(Markdown::options())->script(Markdown::script());

        $this->currency('original_price')->symbol(app_currency_symbol())->default(9999);
        $this->currency('price')->symbol(app_currency_symbol())->default(9999);
        $this->number('stock')->default(999)->min(0);
        $this->number('sort')->default(99);
        $this->switch('status');

        if ($id) {
            $this->display('created_at');
            $this->display('updated_at');
        } else {
            $this->hidden('product_id')->value($productId);
        }
    }

    public function default()
    {
        return [
            'title' => __t('Untitled'),
        ];
    }
}
