<?php

namespace App\Http\Controllers;

use App\Services\GoogleProductFeed;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    /**
     * Google Merchant Center pulls this on its own schedule (daily by default),
     * so it is cached rather than rebuilt per request. Saving a part clears the
     * cache, so marking stock sold takes effect on the next fetch instead of
     * waiting out the TTL.
     */
    public function google(GoogleProductFeed $feed): Response
    {
        $xml = Cache::remember(
            GoogleProductFeed::CACHE_KEY,
            now()->addHour(),
            fn () => $feed->build(),
        );

        // Nothing links to this URL, so a request here is Merchant Center
        // collecting the feed. The admin dashboard reports it as the sign that
        // Google is still picking the feed up.
        Cache::put(
            GoogleProductFeed::FETCHED_AT_KEY,
            now()->toIso8601String(),
            now()->addDays(GoogleProductFeed::FETCHED_AT_TTL_DAYS),
        );

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
