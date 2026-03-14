<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'key' => 'about',
                'title' => 'About Us',
                'content' => '<p>G&M Auto Spares Ltd is a dismantling business situated in Te Awamutu, Waikato, New Zealand.</p>
<p>We are one of New Zealand\'s leading dismantlers of General Motors vehicles with over 2 acres &amp; approx 700 cars in stock. Starting as a small yard in 1983 by Graeme &amp; Mary, they have now been in business for 30 years and have grown considerably.</p>
<p>We also incorporate other popular makes and models, outlined below. Our range comprises vehicles from 1997–2013.</p>
<ul>
<li><strong>GM:</strong> HSV, Commodore, Astra, Vectra, Barina, Epica, Cruze, Captiva, Viva, Opel, Combo, Adventra, Crewman</li>
<li><strong>Chrysler:</strong> 300c, PT Cruiser, Voyager Van</li>
<li><strong>Hyundai:</strong> Late Sonata, Elantra, 4wd Tucson &amp; Santa Fe</li>
<li><strong>Daihatsu:</strong> Terios, Charade, Sirion, Yrv</li>
<li><strong>Suzuki:</strong> Late Swifts, SX4, Aerio</li>
</ul>
<p>We have new stock arriving daily, so if we cannot supply today we may be able to in the near future.</p>
<p>We ship NZ wide using all major courier &amp; freight companies and also ship overseas. We offer fast &amp; friendly service from our knowledgeable staff.</p>',
                'meta_description' => 'About G&M Auto Spares Ltd – Te Awamutu dismantler, GM specialists, 700+ cars in stock. Quality used parts NZ-wide and overseas.',
            ],
            [
                'key' => 'contact',
                'title' => 'Contact Us',
                'content' => '<p>Get in touch for parts enquiries or to find out what we have in stock. Use the form below or give us a call.</p>',
                'meta_description' => 'Contact G&M Autospares for parts enquiries.',
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['key' => $page['key']], $page);
        }
    }
}
