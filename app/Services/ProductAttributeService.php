<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductAttributeGroupRepository;
use App\Repositories\ProductAttributeRepository;
use App\Repositories\ProductAttributeValueRepository;
use Illuminate\Database\Eloquent\Collection;

final class ProductAttributeService
{
    public function __construct(
        protected ProductAttributeGroupRepository $attributeGroupRepository,
        protected ProductAttributeRepository $attributeRepository,
        protected ProductAttributeValueRepository $attributeValueRepository,
    ) {}

    public static function attributeCacheKey(string $groupId, string $key): string
    {
        return sprintf('%s.attributes.%s_%s', appsolutely(), $groupId, $key);
    }

    public function findAttributesByGroupId(int|string $groupId): array
    {
        $data         = $this->attributeGroupRepository->with(['attributes.values'])->find($groupId);
        $attributes   =  $data?->attributes;
        $array        = self::collection2Array($attributes);
        $combinations = self::combinations($array);

        return self::formattedCombination($combinations, $groupId);
    }

    protected function collection2Array(?Collection $attributes): array
    {
        if ($attributes === null) {
            return [];
        }

        return $attributes->map(function ($attribute) {
            $item           = $attribute->toArray();
            $item['values'] = collect($item['values'])->map(function ($value) use ($attribute) {
                return array_merge($value, [
                    'attribute_title' => $attribute->title,
                    'attribute_slug'  => $attribute->slug,
                ]);
            })->all();

            return $item;
        })->all();
    }

    protected function combinations(array $array): array
    {
        $valueSets = array_map(fn ($attr) => $attr['values'], $array);

        return \Arr::crossJoin(...$valueSets);
    }

    protected function formattedCombination(array $combinations, string $groupId): array
    {
        return array_reduce($combinations, function ($item, $combo) use ($groupId) {
            $keys       = http_build_query(collect($combo)->sortBy('attribute_id')->pluck('id', 'attribute_id')->toArray());
            $titles     = http_build_query(collect($combo)->sortBy('attribute_title')->pluck('value', 'attribute_title')->toArray());
            $slugs      = http_build_query(collect($combo)->sortBy('attribute_slug')->pluck('slug', 'attribute_slug')->toArray());
            $key        = self::attributeCacheKey($groupId, $keys);
            $item[$key] =  [
                'key'      => $key,
                'keys'     => $keys,
                'title'    => $titles,
                'slug'     => $slugs,
                'readable' => str_replace(['&', '='], ['; ', ': '], $titles),
                'data'     => $combo,
            ];
            cache([$key => $item[$key]]);

            return $item;
        });
    }

    protected function flattenCombination(array $combinations, string $field): array
    {
        return array_map(fn ($item) => $item[$field], $combinations);
    }
}
