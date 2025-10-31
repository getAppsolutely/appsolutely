<?php

namespace App\Models;

use App\Constants\BasicConstant;
use App\Enums\BlockScope;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Page extends Model
{
    use ScopePublished;
    use ScopeReference;
    use ScopeStatus;
    use Sluggable;

    protected $fillable = [
        'title',
        'name',
        'slug',
        'description',
        'keywords',
        'content',
        'setting',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'structured_data',
        'hreflang',
        'language',
        'parent_id',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'expired_at'      => 'datetime',
        'status'          => 'integer',
        'setting'         => 'array',
        'structured_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->setting)) {
                $page->setting = self::generateDefaultSetting(self::getStructure());
            }
        });

        // Clear sitemap cache when page is saved or deleted
        static::saved(function () {
            app(\App\Services\SitemapService::class)->clearCache();
        });

        static::deleted(function () {
            app(\App\Services\SitemapService::class)->clearCache();
        });
    }

    protected static function generateDefaultSetting(): array
    {
        $structure  = self::getStructure();
        $components = self::attachGlobalBlocks();
        Arr::set($structure, BasicConstant::PAGE_GRAPESJS_KEY, $components);

        return $structure;
    }

    /**
     * Get the basic page builder structure without components
     */
    protected static function getStructure(): array
    {
        return [
            'pages' => [
                [
                    'id'     => Str::random(16),
                    'type'   => 'main',
                    'frames' => [
                        [
                            'id'        => Str::random(16),
                            'component' => [
                                'head' => [
                                    'type' => 'head',
                                ],
                                'type'  => 'wrapper',
                                'docEl' => [
                                    'tagName' => 'html',
                                ],
                                'stylable' => [
                                    'background',
                                    'background-color',
                                    'background-image',
                                    'background-repeat',
                                    'background-attachment',
                                    'background-position',
                                    'background-size',
                                ],
                                'reference'  => 'wrapper-' . Str::random(7),
                                'components' => [],
                            ],
                        ],
                    ],
                ],
            ],
            'assets'      => [],
            'styles'      => [],
            'symbols'     => [],
            'dataSources' => [],
        ];
    }

    /**
     * Attach global blocks to the page structure
     */
    protected static function attachGlobalBlocks(): array
    {
        $blockIds = PageBlockSetting::query()
            ->whereHas('block', function ($query) {
                $query->where('scope', BlockScope::Global->value)->status();
            })
            ->status()
            ->orderBy('sort')
            ->pluck('block_id')
            ->unique();

        $globalBlocks = PageBlock::query()
            ->where('scope', BlockScope::Global->value)
            ->whereIn('id', $blockIds)
            ->status()
            ->orderBy('sort')
            ->get();

        return $globalBlocks->map(function ($block) {
            return [
                'type'      => $block->reference,
                'block_id'  => $block->id,
                'droppable' => $block->droppable ?? 0,
                'reference' => $block->reference . '-' . Str::random(7),
            ];
        })->toArray();
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlockSetting::class);
    }
}
