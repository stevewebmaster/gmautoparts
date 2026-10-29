<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Part;
use App\Models\Vehicle;
use App\Services\PartPresenter;
use Illuminate\Http\Response;

/**
 * robots.txt and sitemap.xml, so search engines find stock as it is loaded
 * rather than whenever they next stumble on a link to it.
 *
 * Every URL is rooted at APP_URL (PartPresenter::absolute), whichever host the
 * request came in on, so the sitemap only ever lists the canonical address.
 */
class SeoController extends Controller
{
    public function robots(): Response
    {
        // Anywhere but the live site, keep crawlers out entirely.
        if (! app()->isProduction()) {
            return $this->text("User-agent: *\nDisallow: /\n");
        }

        $lines = [
            'User-agent: *',
            // The Parts Loader, the admin, and pages holding a customer's details.
            'Disallow: /app',
            'Disallow: /admin',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /reservations',
            // A design variant of the home page.
            'Disallow: /home-v2',
            '',
            'Sitemap: ' . PartPresenter::absolute(route('sitemap', absolute: false)),
        ];

        return $this->text(implode("\n", $lines) . "\n");
    }

    public function sitemap(): Response
    {
        $urls = [
            ['loc' => '/'],
            ['loc' => route('parts.index', absolute: false)],
            ['loc' => route('vehicles.index', absolute: false)],
            ['loc' => route('about', absolute: false)],
            ['loc' => route('contact', absolute: false)],
        ];

        foreach (Page::whereIn('key', ['returns-policy', 'shipping-policy', 'terms-and-conditions'])->get() as $page) {
            $urls[] = ['loc' => route('pages.show', $page->key, absolute: false), 'lastmod' => $page->updated_at];
        }

        // Sold parts keep their page but are a dead end, so only stock that is
        // still for sale is listed.
        Part::query()->listable()->select(['id', 'slug', 'updated_at'])->chunkById(500, function ($parts) use (&$urls) {
            foreach ($parts as $part) {
                $urls[] = ['loc' => route('parts.show', $part->slug, absolute: false), 'lastmod' => $part->updated_at];
            }
        });

        foreach (Vehicle::where('is_visible', true)->get(['id', 'updated_at']) as $vehicle) {
            $urls[] = ['loc' => route('vehicles.show', $vehicle, absolute: false), 'lastmod' => $vehicle->updated_at];
        }

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars(PartPresenter::absolute($url['loc']), ENT_XML1) . '</loc>';

            if (! empty($url['lastmod'])) {
                $xml[] = '    <lastmod>' . $url['lastmod']->toDateString() . '</lastmod>';
            }

            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return response(implode("\n", $xml) . "\n", 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    protected function text(string $body): Response
    {
        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
