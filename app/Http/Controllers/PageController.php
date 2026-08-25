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

    /**
     * Generic editable page, used for the returns/shipping policies Google
     * requires. Route-model-bound on `key` and whitelisted in routes/web.php.
     */
    public function show(Page $page): View
    {
        return view('pages.show', ['page' => $page]);
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
