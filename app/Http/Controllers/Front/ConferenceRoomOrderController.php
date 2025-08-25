<?php

namespace App\Http\Controllers\Front;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;

class ConferenceRoomOrderController extends Controller
{
    protected $productType = ProductTypeEnum::CONFERENCE_ROOM->value;
    public function index()
    {
        $user = auth()->user();
        $userOrderDetails  = $user->orderDetails()->where('product_type', $this->productType)->latest()->paginate(); 
        return view('front.orders.conference-order', compact('userOrderDetails'));
    }

    public function show(string $ref_no)
    { 
        $orderDetail  = OrderDetail::where('product_type', $this->productType)
        ->where('ref_no', $ref_no)
        ->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id());
        })->first();
        $order = $orderDetail->order; 
        $features = json_decode($orderDetail->features, true);

        return view('front.order-details.conference-room', compact('order', 'orderDetail',  'features'));
    }
}
