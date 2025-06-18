<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class Menu extends Model
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

        self::creating(function ($menu) {
            if (empty($menu->reference)) {
                $menu->reference = self::generateReference($menu->title);
            }
        });
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('left');
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
