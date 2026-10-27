<?php

namespace App\Services;

use App\Enums\FeedStatus;
use App\Models\Part;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Answers "what is actually in the Google feed, and why is the rest not?".
 *
 * This is what the admin dashboard reports. It deliberately describes what we
 * *send* Google, not what Google *accepted* — approvals, disapprovals and
 * policy warnings live in Merchant Center and would need the Content API for
 * Shopping to read. Anything shown from here must not imply otherwise.
 */
class FeedHealth
{
    /** @var array<string,int>|null */
    protected ?array $counts = null;

    /**
     * One count per {@see FeedStatus}, keyed by case value.
     *
     * The cases partition the parts table, so these sum to the catalogue total.
     *
     * @return array<string,int>
     */
    public function counts(): array
    {
        if ($this->counts !== null) {
            return $this->counts;
        }

        $counts = [];
        foreach (FeedStatus::cases() as $case) {
            $counts[$case->value] = $case->constrain(Part::query())->count();
        }

        return $this->counts = $counts;
    }

    public function count(FeedStatus $status): int
    {
        return $this->counts()[$status->value] ?? 0;
    }

    public function inFeed(): int
    {
        return $this->count(FeedStatus::InFeed);
    }

    /** Parts that ought to be in the feed and are only missing something. */
    public function needsAttention(): int
    {
        $total = 0;
        foreach (FeedStatus::cases() as $case) {
            if ($case->isFixable()) {
                $total += $this->count($case);
            }
        }

        return $total;
    }

    /** Every part, however it is classified. */
    public function total(): int
    {
        return array_sum($this->counts());
    }

    /**
     * When the feed URL was last requested — in practice, when Merchant Center
     * last collected it. Null means not since the cache was last cleared, which
     * is not the same as never: a deploy or a `cache:clear` resets it.
     */
    public function lastFetchedAt(): ?CarbonInterface
    {
        $stamp = Cache::get(GoogleProductFeed::FETCHED_AT_KEY);

        if (! is_string($stamp) || $stamp === '') {
            return null;
        }

        try {
            return Carbon::parse($stamp);
        } catch (\Throwable) {
            return null;
        }
    }

    public function feedUrl(): string
    {
        return route('feeds.google');
    }
}
