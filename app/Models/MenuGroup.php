<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class MenuGroup extends Model
{
    use ScopeStatus;

    protected $fillable = [
        'title',
        'reference',
        'remark',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($menuGroup) {
            if (empty($menuGroup->reference)) {
                $menuGroup->reference = self::generateReference($menuGroup->title);
            }
        });
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->orderBy('left');
    }

    private static function generateReference(string $title): string
    {
        $baseReference = Str::slug($title);
        $reference     = $baseReference;
        $counter       = 1;

        // Check if reference already exists and append number if needed
        while (self::where('reference', $reference)->exists()) {
            $reference = $baseReference . '-' . $counter;
            $counter++;
        }

        return $reference;
    }
}
