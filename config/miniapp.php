<?php

return [
    /*
    | PIN to access the mini-app (add parts/vehicles from phone).
    | Set MINIAPP_PIN in .env. If empty, mini-app is disabled.
    */
    'pin' => env('MINIAPP_PIN', ''),

    /*
    | Resize/compress uploads with Intervention Image (needs GD or Imagick on the server).
    | Set MINIAPP_OPTIMIZE_UPLOADS=false if saves fail with 500 or “could not save images”.
    */
    'optimize_uploads' => filter_var(env('MINIAPP_OPTIMIZE_UPLOADS', 'true'), FILTER_VALIDATE_BOOLEAN),

    /*
    | Temporary PHP memory limit while Intervention/GD processes an upload (if host allows ini_set).
    */
    'image_memory_limit' => env('MINIAPP_IMAGE_MEMORY_LIMIT', '256M'),
];
