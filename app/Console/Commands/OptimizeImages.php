<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize
                            {--dir=* : Limit to these subfolders of the public disk (default: parts, vehicles)}';

    protected $description = 'Resize oversized uploaded images down to web size (runs out of the request, e.g. via cron).';

    public function handle(): int
    {
        // CLI is not bound by the web request limits, so give the decoder room.
        @ini_set('memory_limit', '512M');

        $dirs = $this->option('dir') ?: ['parts', 'vehicles'];
        $disk = Storage::disk('public');

        $optimized = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($dirs as $dir) {
            if (! $disk->exists($dir)) {
                continue;
            }

            foreach ($disk->files($dir) as $relativePath) {
                if (! preg_match('/\.(jpe?g|png|webp|gif)$/i', $relativePath)) {
                    continue;
                }

                $result = ImageOptimizer::optimizeInPlace($disk->path($relativePath));

                match ($result) {
                    'optimized' => $optimized++,
                    'failed'    => $failed++,
                    default     => $skipped++,
                };

                if ($result === 'optimized') {
                    $this->line("  optimized: {$relativePath}");
                } elseif ($result === 'failed') {
                    $this->warn("  could not process: {$relativePath}");
                }
            }
        }

        $this->info("Done. Optimized {$optimized}, skipped {$skipped} (already small), failed {$failed}.");

        return self::SUCCESS;
    }
}
