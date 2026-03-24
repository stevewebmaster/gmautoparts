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
     * Optimize an uploaded image and store it on the public disk.
     * Returns the stored path (e.g. 'parts/abc123.jpg') or null on failure.
     */
    public static function optimizeAndStore(UploadedFile $file, string $directory): ?string
    {
        try {
            if (! config('miniapp.optimize_uploads', true)) {
                return $file->store($directory, 'public');
            }

            $filename = Str::random(20) . '.jpg';
            $path = $directory . '/' . $filename;
            $fullPath = Storage::disk('public')->path($path);

            if (! Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            try {
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
}
