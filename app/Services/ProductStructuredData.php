<?php

namespace App\Services;

use App\Enums\PartStatus;
use App\Models\Part;

/**
 * schema.org Product markup for a part page.
 *
 * This works independently of Merchant Center: Google supports product rich
 * results from page markup alone. Pages where the part can actually be reserved
 * are eligible as merchant listings; sold or unpriced parts still qualify as
 * product snippets, which is the variant intended for pages you cannot buy from.
 *
 * Values come from PartPresenter, the same source the Merchant feed uses, so the
 * landing page and the feed item always agree.
 */
class ProductStructuredData
{
    public static function for(Part $part): array
    {
        $images = PartPresenter::imageUrls($part);

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => PartPresenter::title($part),
            'description' => PartPresenter::description($part),
            'sku' => PartPresenter::id($part),
            'url' => PartPresenter::url($part),
            'itemCondition' => 'https://schema.org/UsedCondition',
        ];

        if ($images) {
            $data['image'] = $images;
        }

        if ($category = PartPresenter::productType($part)) {
            $data['category'] = $category;
        }

        if ($part->stock_number) {
            $data['identifier'] = $part->stock_number;
        }

        // Vehicle fitment is the thing buyers actually search on, so surface it
        // as additional properties rather than burying it in the description.
        $fitment = array_filter([
            'Make' => $part->make,
            'Model' => $part->model,
            'Year' => $part->year,
        ]);

        if ($fitment) {
            $data['additionalProperty'] = array_map(
                fn ($name, $value) => [
                    '@type' => 'PropertyValue',
                    'name' => $name,
                    'value' => (string) $value,
                ],
                array_keys($fitment),
                $fitment,
            );
        }

        // An Offer without a price is invalid, so unpriced parts get Product
        // markup only — still valid, just not merchant-listing eligible.
        if ($part->price > 0) {
            $data['offers'] = [
                '@type' => 'Offer',
                'url' => PartPresenter::url($part),
                'price' => number_format((float) $part->price, 2, '.', ''),
                'priceCurrency' => GoogleProductFeed::CURRENCY,
                'availability' => self::availability($part),
                'itemCondition' => 'https://schema.org/UsedCondition',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => (string) config('app.name'),
                ],
            ];
        }

        return $data;
    }

    protected static function availability(Part $part): string
    {
        return match ($part->status) {
            PartStatus::Available => 'https://schema.org/InStock',
            PartStatus::OnHold => 'https://schema.org/OutOfStock',
            PartStatus::Sold => 'https://schema.org/SoldOut',
            default => 'https://schema.org/OutOfStock',
        };
    }

    /** Breadcrumb trail matching the on-page breadcrumb. */
    public static function breadcrumbs(Part $part): array
    {
        $trail = [
            ['name' => 'Home', 'url' => PartPresenter::absolute('/')],
            ['name' => 'Parts', 'url' => PartPresenter::absolute(route('parts.index', absolute: false))],
            ['name' => $part->title, 'url' => PartPresenter::url($part)],
        ];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(
                fn (int $i, array $crumb) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                ],
                array_keys($trail),
                $trail,
            ),
        ];
    }
}
