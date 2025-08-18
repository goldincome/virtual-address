<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;

class MeetingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $meetingRooms = $product->meeting_rooms;
        return view('front.meeting-rooms.index', compact('meetingRooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CartService $cartService)
    { //dd($request->all());
        $product = Product::where('id', $request->product_id)->first();
        
        try{
            $cartService->addRoomToCart($product, ProductTypeEnum::MEETING_ROOM->value, $request->all());
        } catch (\Exception $e) {
            // Log::error('Error adding product to cart: ' . $e->getMessage()); // Good practice to log
            return redirect()->back()->with('error', 'Error adding product to cart. Please try again.'.$e->getMessage())->withInput();
        }
        return redirect()->route('cart.index')->with('success', $product->name . ' added to cart.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    { 
        $meetingRoom = Product::whereSlug($slug)->first();
        return view('front.meeting-rooms.show', compact('meetingRoom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
