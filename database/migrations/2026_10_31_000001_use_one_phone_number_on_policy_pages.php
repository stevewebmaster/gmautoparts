<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The policy pages were seeded with 07 849 8814, while the header, footer
     * and Contact page showed (07) 871-8575, the number G&M want customers to
     * call. Only the number is swapped, wherever it appears in the stored page
     * text, so pages edited in the admin are corrected too and nothing else in
     * them changes.
     */
    protected const SWAPS = [
        '07 849 8814' => '(07) 871-8575',
        'tel:+6478498814' => 'tel:+6478718575',
    ];

    public function up(): void
    {
        foreach (self::SWAPS as $old => $new) {
            $this->swap($old, $new);
        }
    }

    public function down(): void
    {
        foreach (self::SWAPS as $old => $new) {
            $this->swap($new, $old);
        }
    }

    protected function swap(string $from, string $to): void
    {
        $pages = DB::table('pages')->where('content', 'like', '%' . $from . '%')->get(['id', 'content']);

        foreach ($pages as $page) {
            DB::table('pages')->where('id', $page->id)->update([
                'content' => str_replace($from, $to, $page->content),
            ]);
        }
    }
};
