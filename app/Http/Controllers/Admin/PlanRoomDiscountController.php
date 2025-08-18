<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Enums\ProductTypeEnum;
use App\Enums\DiscountTypeEnum;
use App\Models\PlanRoomDiscount;
use App\Http\Controllers\Controller;
use App\Services\PlanRoomDiscountService;
use App\Http\Requests\PlanRoomDiscounts\StorePlanRoomDiscountRequest;
use App\Http\Requests\PlanRoomDiscounts\UpdatePlanRoomDiscountRequest;

class PlanRoomDiscountController extends Controller
{
    protected $planRoomDiscountService;

    public function __construct(PlanRoomDiscountService $planRoomDiscountService)
    {
        $this->planRoomDiscountService = $planRoomDiscountService;
    }

    public function index()
    {
        $discounts = $this->planRoomDiscountService->getDiscounts();
        //dd($discounts); // Debugging line, remove in production
        return view('admin.plan-room-discounts.index', compact('discounts'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        $rooms = Product::whereIn('type', [ProductTypeEnum::MEETING_ROOM, ProductTypeEnum::CONFERENCE_ROOM])->get();
        $discountTypes = DiscountTypeEnum::cases();

        return view('admin.plan-room-discounts.create', compact('plans', 'rooms', 'discountTypes'));
    }

    public function store(StorePlanRoomDiscountRequest $request)
    {
        $this->planRoomDiscountService->createDiscount($request->validated());

        return redirect()->route('admin.plan-room-discounts.index')
                         ->with('success', 'Room discount created successfully.');
    }

    public function edit(PlanRoomDiscount $planRoomDiscount)
    {
        $plans = Plan::where('is_active', true)->get();
        $rooms = Product::whereIn('type', [ProductTypeEnum::MEETING_ROOM, ProductTypeEnum::CONFERENCE_ROOM])->get();
        $discountTypes = DiscountTypeEnum::cases();

        return view('admin.plan-room-discounts.edit', compact('planRoomDiscount', 'plans', 'rooms', 'discountTypes'));
    }

    public function update(UpdatePlanRoomDiscountRequest $request, PlanRoomDiscount $planRoomDiscount)
    {
        $this->planRoomDiscountService->updateDiscount($planRoomDiscount, $request->validated());

        return redirect()->route('admin.plan-room-discounts.index')
                         ->with('success', 'Room discount updated successfully.');
    }

    public function destroy(PlanRoomDiscount $planRoomDiscount)
    {
        $this->planRoomDiscountService->deleteDiscount($planRoomDiscount);

        return redirect()->route('admin.plan-room-discounts.index')
                         ->with('success', 'Room discount deleted successfully.');
    }
}

