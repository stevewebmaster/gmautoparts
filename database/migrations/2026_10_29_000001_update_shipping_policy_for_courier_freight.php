<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * The shipping policy said freight was "charged at cost" and confirmed
     * before sending, which stopped being true once checkout charged a fixed
     * freight per part. The new text describes what checkout actually does, and
     * carries a [freight-rates] placeholder the page fills from config.
     *
     * Only replaced if the page is still the text it was seeded with: if G&M
     * have edited it in the admin, their version is left alone and this is
     * logged instead, for someone to merge by hand.
     */
    public function up(): void
    {
        $this->swap(self::SEEDED, self::UPDATED);
    }

    public function down(): void
    {
        $this->swap(self::UPDATED, self::SEEDED);
    }

    protected function swap(string $from, string $to): void
    {
        $page = DB::table('pages')->where('key', 'shipping-policy')->first();

        if (! $page) {
            return;
        }

        if (trim(str_replace("\r\n", "\n", (string) $page->content)) !== trim($from)) {
            Log::warning('Shipping policy has been edited in the admin, so it was not updated. Add the [freight-rates] placeholder and the new freight wording by hand — see database/seeders/PolicyPageSeeder.php.');

            return;
        }

        DB::table('pages')->where('key', 'shipping-policy')->update(['content' => $to, 'updated_at' => now()]);
    }

    private const SEEDED = <<<'HTML'
<p>We ship New Zealand wide using all major courier and freight companies, and we also ship overseas. You are also very welcome to collect from our yard in Te Awamutu.</p>

<h3>Collection (free)</h3>
<p>Parts reserved on this website are held for you to collect from Te Awamutu. There is <strong>no charge</strong> to collect, and <strong>you pay for the part when you pick it up</strong>. Please bring your reservation reference.</p>

<h3>Delivery within New Zealand</h3>
<p>Freight is charged at cost and depends on the size and weight of the part and where it is going. Small parts typically ship for <strong>$10&ndash;$25</strong>; larger items such as panels, doors, bumpers and engines are quoted individually.</p>
<ul>
<li><strong>North Island:</strong> typically 1&ndash;2 working days after dispatch</li>
<li><strong>South Island:</strong> typically 2&ndash;4 working days after dispatch</li>
<li><strong>Rural delivery:</strong> please allow an extra working day</li>
</ul>
<p>Orders are usually dispatched within 1&ndash;2 working days of payment clearing. Oversized items may go by freight company rather than courier.</p>

<h3>Overseas delivery</h3>
<p>We do ship internationally. Freight is quoted case by case &mdash; contact us with the part and the destination and we will come back to you with a price. Any import duties or taxes in the destination country are the buyer&rsquo;s responsibility.</p>

<h3>Getting a freight quote</h3>
<p>Reserve the part on this website or call us on <strong>07 849 8814</strong>, and we will confirm the freight cost before anything is sent.</p>

<h3>Damage in transit</h3>
<p>If a part arrives damaged, contact us within <strong>7 days</strong> with photos and we will arrange a replacement or refund. See our <a href="/returns-policy">Returns Policy</a>.</p>

<h3>Contact</h3>
<p>G&amp;M Auto Spares Ltd, Te Awamutu &middot; Phone <strong>07 849 8814</strong></p>
HTML;

    private const UPDATED = <<<'HTML'
<p>We courier parts New Zealand wide with NZ Couriers, send larger items by truck, and ship overseas on request. You are also very welcome to collect from our yard in Te Awamutu.</p>

<h3>Collection (free)</h3>
<p>Buy online and choose <strong>I&rsquo;ll collect</strong> at checkout, or reserve a part on this website and pay when you pick it up. There is <strong>no charge</strong> to collect. Please bring your order or reservation reference.</p>

<h3>Courier delivery within New Zealand</h3>
<p>Every part sold online has its own freight price, set by the satchel or box it is sent in and whether it is going to the North or South Island. You will see it on the part&rsquo;s page and at checkout, before you pay.</p>
<p>[freight-rates]</p>
<p>Buying several parts? Each part is charged its own freight, because that is how the courier charges us. If we can pack your parts together for less, we refund the difference.</p>

<h3>Delivery times</h3>
<ul>
<li><strong>North Island:</strong> typically 1&ndash;2 working days after dispatch</li>
<li><strong>South Island:</strong> typically 2&ndash;4 working days after dispatch</li>
<li><strong>Rural delivery:</strong> please allow an extra working day</li>
</ul>
<p>Orders are usually dispatched within 1&ndash;2 working days of payment clearing.</p>

<h3>Large items by truck</h3>
<p>Bumpers, doors, panels, gearboxes, engines and other large items are too big for a courier and go by truck. The cost depends on where they are going, so they are not sold online. Send us an enquiry from the part&rsquo;s page or call us on <strong>07 849 8814</strong>, and we will work out the freight to you first, so you know the full cost before you pay.</p>

<h3>Overseas delivery</h3>
<p>We do ship internationally. Freight is quoted case by case &mdash; contact us with the part and the destination and we will come back to you with a price. Any import duties or taxes in the destination country are the buyer&rsquo;s responsibility.</p>

<h3>Getting a freight quote</h3>
<p>Reserve the part on this website or call us on <strong>07 849 8814</strong>, and we will confirm the freight cost before anything is sent.</p>

<h3>Damage in transit</h3>
<p>If a part arrives damaged, contact us within <strong>7 days</strong> with photos and we will arrange a replacement or refund. See our <a href="/returns-policy">Returns Policy</a>.</p>

<h3>Contact</h3>
<p>G&amp;M Auto Spares Ltd, Te Awamutu &middot; Phone <strong>07 849 8814</strong></p>
HTML;
};
