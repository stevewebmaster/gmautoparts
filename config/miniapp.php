<?php

return [
    /*
    | PIN to access the mini-app (add parts/vehicles from phone).
    | Set MINIAPP_PIN in .env. If empty, mini-app is disabled.
    */
    'pin' => env('MINIAPP_PIN', ''),

    /*
    | Resize/compress uploads INLINE during the upload request. Default OFF:
    | decoding a large phone photo can exhaust PHP memory mid-request (a fatal,
    | uncatchable error → 500). Instead the upload stores the original and the
    | `images:optimize` command (scheduled every 5 min) resizes it out of the
    | request. Only set MINIAPP_OPTIMIZE_UPLOADS=true on hosts that can handle
    | inline decoding comfortably.
    */
    'optimize_uploads' => filter_var(env('MINIAPP_OPTIMIZE_UPLOADS', 'false'), FILTER_VALIDATE_BOOLEAN),

    /*
    | Temporary PHP memory limit while Intervention/GD processes an upload (if host allows ini_set).
    */
    'image_memory_limit' => env('MINIAPP_IMAGE_MEMORY_LIMIT', '256M'),
];
