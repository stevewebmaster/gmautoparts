<?php

namespace App\Services;

use App\Enums\PartStatus;
use App\Models\Part;

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
 */
class GoogleProductFeed
{
    public const CACHE_KEY = 'feeds.google.products';

    /** ISO 4217 code appended to every price. */
    public const CURRENCY = 'NZD';

    /** Google product taxonomy path for used vehicle parts. */
    public const GOOGLE_CATEGORY = 'Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts';

    /** Google accepts up to 10 additional images per item. */
    public const MAX_ADDITIONAL_IMAGES = 10;

    public function build(): string
    {
        $items = [];

        Part::query()
            ->listable()
            ->whereNotNull('price')
            ->where('price', '>', 0)
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
            'price' => number_format((float) $part->price, 2, '.', '') . ' ' . self::CURRENCY,
            'condition' => 'used',
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

        $xml[] = '    </item>';

        return implode("\n", $xml);
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
