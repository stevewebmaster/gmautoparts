<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /** Max width in pixels; height scales to keep aspect ratio. */
    public const MAX_WIDTH = 1200;

    /** JPEG quality (1–100). */
    public const JPEG_QUALITY = 82;

    /**
     * Store an uploaded image and return its path (e.g. 'parts/abc123.jpg').
     *
     * By default we store the ORIGINAL as-is and do NOT resize here: decoding a
     * large phone photo can exhaust PHP's memory mid-request, which is a fatal
     * error (uncatchable) that surfaces as a 500. The heavy resizing is done
     * later, out of the request, by the `images:optimize` command (cron sweep).
     *
     * Set MINIAPP_OPTIMIZE_UPLOADS=true to opt back into inline resizing on
     * hosts that can comfortably handle it.
     */
    public static function optimizeAndStore(UploadedFile $file, string $directory): ?string
    {
        try {
            if (! config('miniapp.optimize_uploads', false)) {
                // Fast, low-memory: just move the temp file onto the public disk.
                return $file->store($directory, 'public');
            }

            $filename = Str::random(20) . '.jpg';
            $path = $directory . '/' . $filename;
            $fullPath = Storage::disk('public')->path($path);

            if (! Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            try {
                @ini_set('memory_limit', config('miniapp.image_memory_limit', '256M'));
                @set_time_limit(120);

                $image = Image::read($file);
                $image->scaleDown(width: self::MAX_WIDTH);
                $image->save($fullPath, quality: self::JPEG_QUALITY);

                return $path;
            } catch (\Throwable) {
                return $file->store($directory, 'public');
            }
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resize an already-stored image (by absolute path) down to MAX_WIDTH in
     * place, keeping its format. Returns:
     *   'optimized'  – was oversized, now resized + recompressed
     *   'skipped'    – already within MAX_WIDTH, left untouched (idempotent)
     *   'failed'     – could not read/process (e.g. HEIC without imagick); left untouched
     *
     * Meant to run from the CLI (images:optimize command), where memory/time
     * limits are relaxed.
     */
    public static function optimizeInPlace(string $absolutePath): string
    {
        try {
            $image = Image::read($absolutePath);

            if ($image->width() <= self::MAX_WIDTH) {
                return 'skipped';
            }

            $image->scaleDown(width: self::MAX_WIDTH);
            // No explicit format → Intervention keeps the file's existing format,
            // so the stored path/extension (and DB references) stay valid.
            $image->save($absolutePath, quality: self::JPEG_QUALITY);

            return 'optimized';
        } catch (\Throwable) {
            return 'failed';
        }
    }
}
