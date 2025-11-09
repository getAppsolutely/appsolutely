<?php

declare(strict_types=1);

namespace App\Admin\Controllers\Api;

use App\Traits\ApiResponseTrait;
use Dcat\Admin\Http\Controllers\AdminController;

class AdminBaseApiController extends AdminController
{
    use ApiResponseTrait;
}
