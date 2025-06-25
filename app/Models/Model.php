<?php

namespace App\Models;

use App\Models\Traits\LocalizesDateTime;
use App\Models\Traits\UnsetsUnderscoreAttributes;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use LocalizesDateTime;
    use UnsetsUnderscoreAttributes;
}
