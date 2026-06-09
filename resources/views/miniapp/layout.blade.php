<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f3460">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GM Part Loader">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/miniapp-icon-192.png">
    <link rel="icon" type="image/svg+xml" href="/icons/miniapp-icon.svg">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/miniapp-icon-192.png">
    <link rel="manifest" href="/app-manifest.json">
    <title>@yield('title', 'GM Part Loader')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background: #000; min-height: 100vh; color: #1f2937; }
        .app-header { background: #000; color: #fff; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255, 255, 255, 0.12); }
        .app-header-inner { display: flex; align-items: center; gap: 1rem; }
        .app-header-logo { display: block; width: clamp(130px, 40vw, 200px); height: auto; }
        .app-header-name { color: #fff; font-size: 1.4rem; font-weight: 900; letter-spacing: 0.04em; text-transform: uppercase; opacity: 0.9; }
        .app-body { padding: 1rem; padding-bottom: 2rem; max-width: 560px; margin: 0 auto; }
        .card-app { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08); padding: 1rem; }
        .card-app + .card-app { margin-top: 0.9rem; }
        .page-title { font-size: 1.3rem; margin: 0 0 0.65rem; color: #0f172a; }
        .lead-text { color: #6b7280; margin: 0 0 1rem; }
        .btn-app { display: block; width: 100%; padding: 0.9rem 1.1rem; font-size: 1rem; font-weight: 600; text-align: center; border-radius: 12px; border: none; text-decoration: none; cursor: pointer; margin-bottom: 0.65rem; transition: transform 0.1s, box-shadow 0.1s; }
        .btn-app:active { transform: scale(0.98); }
        .btn-primary-app { background: linear-gradient(135deg, #2563eb 0%, #0d6efd 100%); color: #fff; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25); }
        .btn-secondary-app { background: #fff; color: #1f2937; border: 1px solid #d1d5db; }
        .back-link, .muted-link { color: #6b7280; font-size: 0.9rem; text-decoration: none; }
        .back-link { display: inline-block; margin-top: 0.5rem; }
        .muted-link:hover, .back-link:hover { color: #1f2937; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.35rem; font-size: 0.9rem; color: #111827; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.72rem 0.75rem; font-size: 16px; border: 1px solid #d1d5db; border-radius: 10px; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group input[type="file"] { padding: 0.5rem; }
        .alert-success { background: #d1e7dd; color: #0f5132; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-error { background: #f8d7da; color: #842029; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .errors { list-style: none; padding: 0; margin: 0 0 1rem; color: #842029; font-size: 0.9rem; }
        .photo-hint { font-size: 0.85rem; color: #666; margin-top: 0.25rem; }
    </style>
    @stack('styles')
</head>
<body>
    @if(!request()->routeIs('app.login'))
    <header class="app-header">
        <div class="app-header-inner">
            <img class="app-header-logo" src="/gm-parts-logo.svg" alt="G&M Auto Parts">
            <span class="app-header-name">Parts Loader</span>
        </div>
    </header>
    @endif
    <main class="app-body">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <ul class="errors">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
