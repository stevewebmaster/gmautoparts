<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'G&M Autospares'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),

    // Other hostnames the site also answers on (www, the old staging host),
    // which 301 to APP_URL — and while any are set, plain http goes to https.
    // Comma separated; empty switches all of it off.
    'redirect_hosts' => array_values(array_filter(array_map(
        fn ($host) => strtolower(trim($host)),
        explode(',', (string) env('REDIRECT_HOSTS', '')),
    ))),

    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Pacific/Auckland',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_NZ',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
    ],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\Filament\AdminPanelProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->merge([
        //
    ])->toArray(),
];
