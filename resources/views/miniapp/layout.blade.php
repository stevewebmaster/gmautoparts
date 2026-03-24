<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f3460">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="G&M Mini-app">
    <link rel="apple-touch-icon" href="/icons/miniapp-icon.svg">
    <link rel="manifest" href="/app-manifest.json">
    <title>@yield('title', 'G&M Mini-app')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 0; background: #f5f5f5; min-height: 100vh; color: #333; }
        .app-header { background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%); color: #fff; padding: 1rem 1.25rem; font-weight: 700; font-size: 1.25rem; }
        .app-header a { color: #fff; text-decoration: none; }
        .app-body { padding: 1.25rem; padding-bottom: 2rem; max-width: 480px; margin: 0 auto; }
        .btn-app { display: block; width: 100%; padding: 1rem 1.25rem; font-size: 1.1rem; font-weight: 600; text-align: center; border-radius: 12px; border: none; text-decoration: none; cursor: pointer; margin-bottom: 0.75rem; transition: transform 0.1s, box-shadow 0.1s; }
        .btn-app:active { transform: scale(0.98); }
        .btn-primary-app { background: #0d6efd; color: #fff; }
        .btn-secondary-app { background: #fff; color: #333; border: 2px solid #ddd; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.35rem; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; font-size: 16px; border: 1px solid #ccc; border-radius: 8px; }
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
        <a href="{{ route('app.dashboard') }}">G&M Mini-app</a>
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
