<?php

namespace App\Filament\Widgets;

use App\Enums\FeedStatus;
use App\Filament\Resources\PartResource;
use App\Services\FeedHealth;
use Filament\Widgets\Widget;

/**
 * What the Google Merchant feed currently contains, and what is keeping the
 * rest of the catalogue out of it.
 *
 * Each excluded row links to the Parts list filtered to exactly those parts, so
 * "9 parts have no price" is one click away from fixing them.
 */
class GoogleFeedHealth extends Widget
{
    protected static string $view = 'filament.widgets.google-feed-health';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $health = app(FeedHealth::class);

        $rows = [];
        foreach (FeedStatus::excluded() as $case) {
            $count = $health->count($case);

            // A reason nobody is hitting is noise; drop it.
            if ($count === 0) {
                continue;
            }

            $rows[] = [
                'status' => $case,
                'count' => $count,
                'url' => self::partsUrlFor($case),
            ];
        }

        return [
            'inFeed' => $health->inFeed(),
            'needsAttention' => $health->needsAttention(),
            'total' => $health->total(),
            'rows' => $rows,
            'feedUrl' => $health->feedUrl(),
            'lastFetchedAt' => $health->lastFetchedAt(),
            'inFeedUrl' => self::partsUrlFor(FeedStatus::InFeed),
        ];
    }

    protected static function partsUrlFor(FeedStatus $status): string
    {
        return PartResource::getUrl('index', [
            'tableFilters' => ['feed' => ['value' => $status->value]],
        ]);
    }
}
