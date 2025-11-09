<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller as BaseController;

class BaseApiController extends BaseController
{
    use ApiResponseTrait;
}
