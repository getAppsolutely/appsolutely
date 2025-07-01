<?php

namespace App\Admin\Forms\Models;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;

class ProductAttributeValueForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct();
        $this->model = $id ? ProductAttributeValue::find($id) : new ProductAttributeValue();
    }

    public function form(): void
    {
        parent::form();
        $this->hidden('id');

        $this->select('product_attribute_id', 'Attribute')
            ->options(ProductAttribute::status()->pluck('title', 'id'))
            ->required();
        $this->text('value', __t('Value'))->required();
        $this->text('slug', __t('Slug'));
        $this->switch('status', __t('Status'))->default(true);
    }
}
