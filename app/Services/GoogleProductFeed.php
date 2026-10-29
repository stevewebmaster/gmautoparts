<?php

namespace App\Services;

use App\Enums\PartStatus;
use App\Models\Part;
use Illuminate\Support\Facades\Cache;

/**
 * Builds a Google Merchant Center product feed (RSS 2.0 + the g: namespace).
 *
 * Attribute names, allowed values and limits follow Google's product data
 * specification. Two choices worth knowing about:
 *
 *  - Used parts carry no barcode, so brand/gtin/mpn are omitted entirely and
 *    identifier_exists is 'no'. Submitting a stock number as an MPN would be
 *    wrong — it is an internal yard code, not a manufacturer part number.
 *  - Parts with no price are skipped rather than sent at 0.00, because Google
 *    rejects a null or zero price outright.
 *  - Quote-only parts are skipped too: Google requires a working checkout, and
 *    a part with no freight size cannot be bought online.
 */
class GoogleProductFeed
{
    public const CACHE_KEY = 'feeds.google.products';

    /**
     * When the feed URL was last requested. The feed is not linked from
     * anywhere public, so in practice a fetch is Merchant Center collecting it
     * — which makes this the closest thing to proof that Google is pulling the
     * feed without going and asking the Content API. Kept for 60 days so a
     * quiet stretch still shows the real last fetch rather than "never".
     */
    public const FETCHED_AT_KEY = 'feeds.google.fetched_at';

    public const FETCHED_AT_TTL_DAYS = 60;

    /**
     * Drop the cached XML so the next fetch rebuilds it. Does not touch the
     * fetch timestamp — that is a record of Google's behaviour, not ours.
     */
    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** ISO 4217 code appended to every price. */
    public const CURRENCY = 'NZD';

    /** Google product taxonomy path for used vehicle parts. */
    public const GOOGLE_CATEGORY = 'Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts';

    /** Google accepts up to 10 additional images per item. */
    public const MAX_ADDITIONAL_IMAGES = 10;

    /**
     * NZ postcodes split cleanly by island — 0–6 North, 7–9 South — so each
     * checkout region maps to one Google postcode range.
     */
    public const REGION_POSTCODES = [
        'north_island' => '0*-6*',
        'south_island' => '7*-9*',
    ];

    public function build(): string
    {
        $items = [];

        Part::query()
            ->feedable()
            ->with(['category', 'subcategory'])
            ->chunkById(200, function ($parts) use (&$items) {
                foreach ($parts as $part) {
                    // No image means no listing — Google requires image_link.
                    if (! is_array($part->images) || count($part->images) === 0) {
                        continue;
                    }

                    $items[] = $this->item($part);
                }
            });

        $shopName = (string) config('app.name', 'G&M Auto Parts');

        return implode("\n", [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">',
            '  <channel>',
            '    <title>' . $this->escape($shopName . ' — Used Car Parts') . '</title>',
            '    <link>' . $this->escape(PartPresenter::absolute('/')) . '</link>',
            '    <description>' . $this->escape('Quality used car parts from ' . $shopName . '.') . '</description>',
            implode("\n", $items),
            '  </channel>',
            '</rss>',
            '',
        ]);
    }

    protected function item(Part $part): string
    {
        $images = PartPresenter::imageUrls($part);

        $lines = [
            'id' => PartPresenter::id($part),
            'title' => PartPresenter::title($part),
            'description' => PartPresenter::description($part),
            'link' => PartPresenter::url($part),
            'image_link' => $images[0],
            'availability' => $part->status === PartStatus::Available ? 'in_stock' : 'out_of_stock',
            'price' => $this->money((float) $part->price),
            'condition' => PartPresenter::condition($part),
            'identifier_exists' => 'no',
            'google_product_category' => self::GOOGLE_CATEGORY,
        ];

        if ($productType = PartPresenter::productType($part)) {
            $lines['product_type'] = $productType;
        }

        $xml = ['    <item>'];

        foreach ($lines as $tag => $value) {
            $xml[] = '      <g:' . $tag . '>' . $this->escape($value) . '</g:' . $tag . '>';
        }

        foreach (array_slice($images, 1, self::MAX_ADDITIONAL_IMAGES) as $image) {
            $xml[] = '      <g:additional_image_link>' . $this->escape($image) . '</g:additional_image_link>';
        }

        foreach ($this->shipping($part) as $shipping) {
            $xml[] = '      <g:shipping>';

            foreach ($shipping as $tag => $value) {
                $xml[] = '        <g:' . $tag . '>' . $this->escape($value) . '</g:' . $tag . '>';
            }

            $xml[] = '      </g:shipping>';
        }

        $xml[] = '    </item>';

        return implode("\n", $xml);
    }

    /**
     * Freight to each island, at the rates the checkout charges. Without this
     * Merchant Center falls back to the account's shipping settings, which want
     * a weight per product — and parts are sized by courier box, never weighed.
     * A price here overrides the account settings for those postcodes, so no
     * weight is needed.
     *
     * The rural surcharge is left out: it depends on the address rather than
     * the postcode range, so Google shows the urban rate.
     */
    protected function shipping(Part $part): array
    {
        $rates = [];

        foreach (self::REGION_POSTCODES as $region => $postcodes) {
            $rates[] = [
                'country' => 'NZ',
                'postal_code' => $postcodes,
                'service' => 'Courier',
                'price' => $this->money($part->shipping_band->rate($region)),
            ];
        }

        return $rates;
    }

    protected function money(float $amount): string
    {
        return number_format($amount, 2, '.', '') . ' ' . self::CURRENCY;
    }

    /**
     * Escapes for XML text content, first dropping characters that are not legal
     * in XML 1.0 at all (stray control bytes pasted into a description would
     * otherwise produce a feed Google cannot parse).
     */
    protected function escape(string $value): string
    {
        $value = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
