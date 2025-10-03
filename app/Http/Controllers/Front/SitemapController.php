<?php

namespace App\Http\Controllers\Front;

use App\Models\Product;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    public function index()
    {
        $product = app(Product::class);
        $virtualAddress = $product->virtual_address;
        $plans = $virtualAddress->plans()->with(['features', 'media', 'features.featureSetting'])
            ->where('is_active', true)->get();
            
        $meetingRooms = $product->meeting_rooms;
        $conferenceRooms = $product->conference_rooms;
        //dd($meetingRooms, $plans, $conferenceRooms);
        return response()->view('front.sitemap', compact('plans', 'meetingRooms', 'conferenceRooms'))
            ->header('Content-Type', 'text/xml');
    }
}
