<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;

class NestedSetModel extends Model
{
    use NodeTrait;

    protected $defaultParentId = null;

    protected string $orderColumn = 'left';

    public function getLftName(): string
    {
        return 'left';
    }

    public function getRgtName(): string
    {
        return 'right';
    }
}
