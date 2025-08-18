<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;

class MeetingRoomOrderController extends Controller
{
    protected $productType = ProductTypeEnum::MEETING_ROOM->value;
    public function index()
    {
        $user = auth()->user();

        $userOrderDetails  = $user->orderDetails()->where('product_type', $this->productType)->latest()->paginate();
 
        return view('front.orders.meeting-room-order', compact('userOrderDetails'));
    }

    public function show(string $ref_no)
    {
        $user = auth()->user();
        $orderDetail  = $user->orderDetails()->where('product_type', $this->productType)
            ->where('ref_no', $ref_no)->first();
        $order = $orderDetail->order; 
        $features = json_decode($orderDetail->features, true);
        return view('front.order-details.meeting-room', compact('order', 'orderDetail',  'features'));
    }
}
