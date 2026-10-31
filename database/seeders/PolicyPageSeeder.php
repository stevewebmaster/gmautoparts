<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Returns and shipping policies. Google Merchant Center requires both to be
 * published and reachable before it will approve products.
 *
 * ⚠️ THIS IS DRAFT TEXT FOR THE CLIENT TO REVIEW AND APPROVE. The specifics
 * below are conservative defaults, not legal advice, and at least these need
 * confirming with G&M before the feed is submitted:
 *
 *   - the warranty period offered on used mechanical parts (30 days assumed)
 *   - who pays return freight on a change-of-mind return (customer assumed)
 *   - whether change-of-mind returns are accepted at all
 *   - delivery timeframes (the shipping policy's freight table itself is
 *     drawn from config/shipping.php — see the [freight-rates] placeholder)
 *
 * Note that under the NZ Consumer Guarantees Act a trader cannot contract out
 * of the consumer guarantees for consumer sales, including on used goods.
 *
 * Uses firstOrCreate, not updateOrCreate: re-running this must never overwrite
 * edits the client has made in the admin.
 *
 * Run with: php artisan db:seed --class=PolicyPageSeeder
 */
class PolicyPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'key' => 'returns-policy',
                'title' => 'Returns Policy',
                'meta_description' => 'Returns, refunds and warranty on used car parts from G&M Auto Spares, Te Awamutu.',
                'content' => '<p>We want you to get the right part. If something is not right, contact us as soon as possible and we will sort it out.</p>

<h3>Warranty on used parts</h3>
<p>Used mechanical parts are covered by a <strong>30 day warranty</strong> from the date of purchase, covering the part being fit for its normal purpose. Electrical components, and any part sold as "untested" or "for parts only", are sold as-is.</p>
<p>Nothing in this policy limits your rights under the New Zealand <strong>Consumer Guarantees Act 1993</strong>.</p>

<h3>If a part is faulty or not as described</h3>
<p>Contact us within 30 days of purchase with your reservation reference or receipt. We will arrange for the part to be returned to us and inspected. If the part is faulty or was not as described, we will offer a <strong>replacement, an exchange, or a full refund</strong>, whichever you prefer. We pay return freight in this case.</p>

<h3>Change of mind</h3>
<p>If you have ordered the wrong part or no longer need it, contact us within <strong>14 days</strong> of purchase. The part must be unused, unfitted and in the same condition it was supplied in. Return freight is at your cost. Once we receive and check the part we will refund the purchase price.</p>

<h3>How to return a part</h3>
<ol>
<li>Call us on <strong>(07) 871-8575</strong> or email us, quoting your reservation reference.</li>
<li>We will confirm the return address and whether we are covering freight.</li>
<li>Send the part back, or drop it in to us in Te Awamutu.</li>
</ol>

<h3>Refunds</h3>
<p>Refunds are made by the same method you paid, within <strong>10 working days</strong> of us receiving and checking the returned part.</p>

<h3>Reserved parts you did not collect</h3>
<p>Reserving a part on this website costs nothing and does not charge you. If you do not collect a reserved part within the holding period, the reservation simply lapses and the part goes back on sale. There is nothing to refund.</p>

<h3>Contact</h3>
<p>G&amp;M Auto Spares Ltd, Te Awamutu &middot; Phone <strong>(07) 871-8575</strong></p>',
            ],
            [
                'key' => 'shipping-policy',
                'title' => 'Shipping Policy',
                'meta_description' => 'Delivery and collection options for used car parts from G&M Auto Spares, Te Awamutu — NZ-wide and overseas.',
                'content' => '<p>We courier parts New Zealand wide with NZ Couriers, send larger items by truck, and ship overseas on request. You are also very welcome to collect from our yard in Te Awamutu.</p>

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
<p>Bumpers, doors, panels, gearboxes, engines and other large items are too big for a courier and go by truck. The cost depends on where they are going, so they are not sold online. Send us an enquiry from the part&rsquo;s page or call us on <strong>(07) 871-8575</strong>, and we will work out the freight to you first, so you know the full cost before you pay.</p>

<h3>Overseas delivery</h3>
<p>We do ship internationally. Freight is quoted case by case &mdash; contact us with the part and the destination and we will come back to you with a price. Any import duties or taxes in the destination country are the buyer&rsquo;s responsibility.</p>

<h3>Getting a freight quote</h3>
<p>Reserve the part on this website or call us on <strong>(07) 871-8575</strong>, and we will confirm the freight cost before anything is sent.</p>

<h3>Damage in transit</h3>
<p>If a part arrives damaged, contact us within <strong>7 days</strong> with photos and we will arrange a replacement or refund. See our <a href="/returns-policy">Returns Policy</a>.</p>

<h3>Contact</h3>
<p>G&amp;M Auto Spares Ltd, Te Awamutu &middot; Phone <strong>(07) 871-8575</strong></p>',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['key' => $page['key']], $page);
        }
    }
}
