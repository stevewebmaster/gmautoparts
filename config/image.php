<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | GD is bundled with most PHP builds. Imagick requires the imagick extension
    | and ImageMagick on the server — ask your host if unsure.
    |
    | Set in .env: IMAGE_DRIVER=gd  or  IMAGE_DRIVER=imagick
    |
    */

    'driver' => match (strtolower((string) env('IMAGE_DRIVER', 'gd'))) {
        'imagick' => \Intervention\Image\Drivers\Imagick\Driver::class,
        default => \Intervention\Image\Drivers\Gd\Driver::class,
    },

    /*
    |--------------------------------------------------------------------------
    | Configuration Options
    |--------------------------------------------------------------------------
    */

    'options' => [
        'autoOrientation' => true,
        'decodeAnimation' => true,
        'blendingColor' => 'ffffff',
        'strip' => false,
    ],
];
