<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about()
    {
        $page = rescue(
            fn () => Page::where('key', 'about')->first(),
            null,
            false
        );

        return view('pages.about', ['page' => $page]);
    }

    /** Placeholder an editable page can carry where the freight table goes. */
    public const FREIGHT_RATES = '[freight-rates]';

    /**
     * Generic editable page, used for the returns/shipping policies Google
     * requires. Route-model-bound on `key` and whitelisted in routes/web.php.
     *
     * The shipping policy's rates come from config, not the page text, so the
     * policy always quotes what checkout and Google charge.
     */
    public function show(Page $page): View
    {
        $content = (string) $page->content;

        if (str_contains($content, self::FREIGHT_RATES)) {
            $table = view('pages.partials.freight-rates')->render();

            // The admin's editor wraps the placeholder in a paragraph, and a
            // table cannot sit inside one.
            $content = str_replace(['<p>' . self::FREIGHT_RATES . '</p>', self::FREIGHT_RATES], $table, $content);
        }

        return view('pages.show', ['page' => $page, 'content' => $content]);
    }

    public function contact()
    {
        $page = rescue(
            fn () => Page::where('key', 'contact')->first(),
            null,
            false
        );

        return view('pages.contact', ['page' => $page]);
    }
}
