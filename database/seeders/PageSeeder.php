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
                'content' => '<p>Welcome to G&M Autospares. We are your trusted automotive wrecker in New Zealand, offering quality used parts for a wide range of vehicles.</p><p>Edit this content in the Admin panel under Pages.</p>',
                'meta_description' => 'About G&M Autospares - quality used car parts in New Zealand.',
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
