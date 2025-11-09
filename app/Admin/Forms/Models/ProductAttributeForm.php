<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeGroup;

class ProductAttributeForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        $this->relationships = ['attributeGroups'];
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new ProductAttribute();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        $this->text('title', __t('Title'))->required();
        $this->text('slug', __t('Slug'))->help(__t('Leave empty to auto-generate from title'));
        $this->text('remark', __t('Remark'));
        $this->switch('status', __t('Status'))->default(true);

        $this->multipleSelect('attributeGroups', __t('Attribute Groups'))
            ->options(ProductAttributeGroup::status()->pluck('title', 'id'))
            ->customFormat(extract_values());
    }
}
