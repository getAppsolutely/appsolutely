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
            ProductSku::create($input);

            return $this->response()->success(__t('SKU created successfully'))->refresh();
        } catch (\Exception $e) {
            return $this->response()->error(__t('Failed to create SKU: ') . $e->getMessage());
        }
    }

    public function form()
    {
        // Get product_id from payload
        $productId = $this->payload['product_id'] ?? null;
        $this->hidden('product_id')->value($productId);

        $this->text('title')->required();
        $this->text('slug')->help(__t('Leave empty to auto-generate from title'));
        $this->image('cover')->autoUpload()->url(upload_url(ProductSku::class, $productId));
        $this->textarea('keywords')->rows(2);
        $this->textarea('description')->rows(3);
        $this->markdown('content')->options(Markdown::options())->script(Markdown::script());

        $this->currency('original_price')->symbol(app_currency_symbol())->default(9999);
        $this->currency('price')->symbol(app_currency_symbol())->default(9999);
        $this->number('stock')->default(999)->min(0);
        $this->number('sort')->default(99);
        $this->switch('status');
        $this->display('created_at');
        $this->display('updated_at');
    }

    public function default()
    {
        return [
            'title' => __t('Untitled'),
        ];
    }
}
