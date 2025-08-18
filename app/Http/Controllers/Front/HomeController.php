<?php

namespace App\Http\Controllers\Front;

use App\Models\User;
use App\Models\Product;
use Illuminate\View\View;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;


class HomeController extends Controller
{
    public function index(Product $product): View
    {
        $user = User::find(1);
        //get all the virtual addresses
        $virtualAddress = $product->virtual_address;
        $plans = $virtualAddress->plans()->with(['features', 'media', 'features.featureSetting'])->where('is_active', true)->get();

        $meetingRooms = $product->meeting_rooms;

        $conferenceRooms = $product->conference_rooms;
       
        //dd($user->planSubscription('pro')->canUseFeature('listings'));

        return view('front.home', compact('plans', 'meetingRooms', 'conferenceRooms'));
    }
    
  
}
