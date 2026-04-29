<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function about()
    {
        $page = Page::where('key', 'about')->firstOrFail();
        return view('pages.about', ['page' => $page]);
    }

    public function contact()
    {
        $page = Page::where('key', 'contact')->firstOrFail();
        return view('pages.contact', ['page' => $page]);
    }
}
