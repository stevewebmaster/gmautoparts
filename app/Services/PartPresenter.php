<?php

namespace App\Services;

use App\Models\Part;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared presentation of a Part for machine consumers — the Google Merchant
 * feed and the on-page Product structured data.
 *
 * These agree deliberately: Google matches structured data on the landing page
 * against the feed item, so a title or id that differs between the two is worse
 * than either being slightly imperfect.
 */
class PartPresenter
{
    /** Google's title limit; the tightest of the consumers. */
    public const TITLE_LIMIT = 150;

    public const DESCRIPTION_LIMIT = 5000;

    /**
     * Stable, unique identifier. Not the stock number — stock numbers are
     * nullable and not unique in the schema.
     */
    public static function id(Part $part): string
    {
        return 'part-' . $part->id;
    }

    /**
     * Prefixes year/make/model onto the part title, skipping any bit the title
     * already mentions — otherwise "Toyota Hilux Headlight" becomes
     * "2010 Toyota Hilux Toyota Hilux Headlight".
     */
    public static function title(Part $part): string
    {
        $title = trim((string) $part->title);
        $prefix = [];

        foreach ([$part->year, $part->make, $part->model] as $bit) {
            $bit = trim((string) $bit);

            if ($bit !== '' && ! Str::contains(Str::lower($title), Str::lower($bit))) {
                $prefix[] = $bit;
            }
        }

        return Str::limit(trim(implode(' ', $prefix) . ' ' . $title), self::TITLE_LIMIT, '');
    }

    public static function description(Part $part): string
    {
        $description = trim(strip_tags((string) $part->description));

        if ($description === '') {
            $vehicle = trim(implode(' ', array_filter([$part->year, $part->make, $part->model])));

            $description = $vehicle !== ''
                ? "Used {$part->title} removed from a {$vehicle}. Contact us to check fitment for your vehicle."
                : "Used {$part->title}. Contact us to check fitment for your vehicle.";
        }

        return Str::limit($description, self::DESCRIPTION_LIMIT, '');
    }

    /** Absolute image URLs, main image first. */
    public static function imageUrls(Part $part): array
    {
        $images = array_values(array_filter((array) $part->images));

        return array_map(
            fn (string $path) => self::absolute(Storage::disk('public')->url($path)),
            $images,
        );
    }

    public static function url(Part $part): string
    {
        return self::absolute(route('parts.show', $part->slug, absolute: false));
    }

    public static function productType(Part $part): ?string
    {
        $parts = array_filter([
            $part->category?->name,
            $part->subcategory?->name,
        ]);

        return $parts ? implode(' > ', $parts) : null;
    }

    /**
     * Roots a path at APP_URL rather than the incoming request host. The feed is
     * cached, so request-relative URLs would bake in whichever hostname happened
     * to trigger the rebuild.
     *
     * Images must be addressed on the public disk explicitly: FILESYSTEM_DISK is
     * `local` and only the `public` disk defines a url, so a bare Storage::url()
     * yields a relative path.
     */
    public static function absolute(string $pathOrUrl): string
    {
        if (Str::startsWith($pathOrUrl, ['http://', 'https://'])) {
            return $pathOrUrl;
        }

        return rtrim((string) config('app.url'), '/') . '/' . ltrim($pathOrUrl, '/');
    }
}
