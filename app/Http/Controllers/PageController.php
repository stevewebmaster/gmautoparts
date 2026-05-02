<?php

namespace App\Http\Controllers;

use App\Models\Page;

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
