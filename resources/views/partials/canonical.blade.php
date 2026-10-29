{{-- The one address for this page, on APP_URL's host. The site also answers
     on www and the old staging host; this tells Google which copy to index.
     Keeps ?page= so each page of a listing is its own canonical. --}}
@php($listingPage = (int) request()->query('page', 1))
<link rel="canonical" href="{{ rtrim(config('app.url'), '/') . request()->getPathInfo() . ($listingPage > 1 ? '?page=' . $listingPage : '') }}">
