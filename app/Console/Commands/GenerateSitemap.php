<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate a static public/sitemap.xml for search engines';

    public function handle(): int
    {
        $urls = [];

        $static = [
            'home.index' => ['changefreq' => 'weekly', 'priority' => '1.0'],
            'virtual-address.index' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            'local.woolwich' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            'virtual-address.registered-office' => ['changefreq' => 'monthly', 'priority' => '0.8'],
            'virtual-address.mail-forwarding' => ['changefreq' => 'monthly', 'priority' => '0.8'],
            'virtual-address.directors-service' => ['changefreq' => 'monthly', 'priority' => '0.8'],
            'meeting-rooms.index' => ['changefreq' => 'weekly', 'priority' => '0.8'],
            'conference-rooms.index' => ['changefreq' => 'weekly', 'priority' => '0.8'],
            'contact-us.index' => ['changefreq' => 'yearly', 'priority' => '0.5'],
            'about-us.index' => ['changefreq' => 'yearly', 'priority' => '0.5'],
            'terms-of-service.index' => ['changefreq' => 'yearly', 'priority' => '0.2'],
            'privacy-policy.index' => ['changefreq' => 'yearly', 'priority' => '0.2'],
            'guides.registered-rules' => ['changefreq' => 'monthly', 'priority' => '0.6'],
            'guides.comparison' => ['changefreq' => 'monthly', 'priority' => '0.6'],
            'guides.company-registration' => ['changefreq' => 'monthly', 'priority' => '0.6'],
            'guides.best-areas' => ['changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        foreach ($static as $name => $meta) {
            if (! Route::has($name)) {
                continue;
            }
            $urls[] = [
                'loc' => URL::to(trim($name === 'home.index' ? '/' : route($name), '/')),
                'lastmod' => now()->toAtomString(),
                'changefreq' => $meta['changefreq'],
                'priority' => $meta['priority'],
            ];
        }

        $product = app(Product::class);

        $plans = collect();
        $virtualAddress = $product->virtual_address;
        if ($virtualAddress) {
            $plans = $virtualAddress->plans()
                ->where('is_active', true)
                ->get()
                ->map(fn ($plan) => [
                    'loc' => route('virtual-address.show', $plan->slug),
                    'lastmod' => $plan->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ]);
        }

        $meetingRooms = $product->meeting_rooms->map(fn ($room) => [
            'loc' => route('meeting-rooms.show', $room->slug),
            'lastmod' => $room->updated_at?->toAtomString() ?? now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ]);

        $conferenceRooms = $product->conference_rooms->map(fn ($room) => [
            'loc' => route('conference-rooms.show', $room->slug),
            'lastmod' => $room->updated_at?->toAtomString() ?? now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ]);

        $urls = array_merge($urls, $plans->all(), $meetingRooms->all(), $conferenceRooms->all());

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
        foreach ($urls as $url) {
            $node = $xml->addChild('url');
            $node->addChild('loc', $url['loc']);
            $node->addChild('lastmod', $url['lastmod']);
            $node->addChild('changefreq', $url['changefreq']);
            $node->addChild('priority', $url['priority']);
        }

        file_put_contents(public_path('sitemap.xml'), $xml->asXML());

        $this->info('Sitemap generated with ' . count($urls) . ' URLs.');
        return self::SUCCESS;
    }
}