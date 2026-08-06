<?php

use Illuminate\Support\Str;

/*
 | This file was missing, which left cache.default resolving to null and the
 | cache silently falling back to Illuminate\Cache\NullStore — every write was
 | discarded. That quietly disabled anything built on the cache, including the
 | throttle middleware on the contact and enquiry forms and withoutOverlapping()
 | on scheduled commands.
 |
 | Only the drivers this project actually uses are listed. The file store
 | supports atomic locks, which is what withoutOverlapping() needs.
 */
return [
    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
    ],

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_cache_'),
];
