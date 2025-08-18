<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\PlanService;
use App\Enums\ProductTypeEnum;
use App\Models\FeatureSetting;
use App\Services\FeatureService;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRooms\StoreMeetingRoomRequest;
use App\Http\Requests\MeetingRooms\UpdateMeetingRoomRequest;

class MeetingRoomController extends Controller
{
    protected $productType = ProductTypeEnum::MEETING_ROOM;
    protected $storageInfo = [
        'primary_image' => Product::MEETING_PRIMARY_IMAGE,
        'additional_images' =>Product::MEETING_ADDITIONAL_IMAGES,
        'image_folder' => Product::MEETING_IMAGE_FOLDER
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meetingRooms = Product::where('type', ProductTypeEnum::MEETING_ROOM)
            ->with(['features', 'media'])
            ->latest()->paginate(10);
        //foreach($meetingRooms as $meetingRoom){
          //  dd($meetingRoom->media);
        //}
        return view('admin.meeting-rooms.index', compact('meetingRooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {  
        //dd($meetingRooms);
        $featureSettings = FeatureSetting::all();
       
        return view('admin.meeting-rooms.create', 
            [ 'productType' =>$this->productType,
             'featureSettings' => $featureSettings,
             ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMeetingRoomRequest $request, ProductService $productService, PlanService $planService)
    {      //dd($request->all());
         $validated = $request->validated();
        try{
                \DB::transaction(function () use ($request, $validated, $productService, $planService) {
                $productData = $planData = [];

                    if ($request->hasFile('main_product_image')) {
                        $productData['main_image'] =  $request->file('main_product_image') ?? null;
                    }

                    // Store additional images
                    if ($request->hasFile('additional_images')) {
                        $productData['additional_images'] =  $request->file('additional_images') ?? null;
                    }
                    $productData['type'] =  $this->productType->value;
                    
                    $product = $productService->createProduct(
                        array_merge($validated, $productData), 
                        $validated['features'], 
                        $this->storageInfo
                    );

                    $plan = Plan::where('slug', Str::slug(ProductTypeEnum::MEETING_ROOM->label()))->first();
                    
                    if(!$plan){
                        $planData = [
                            'name' => ProductTypeEnum::MEETING_ROOM->label(),
                            'is_active' => false,
                            'price' => 0.00
                        ];
                        $plan = $planService->createPlanForProduct($product, $planData, $validated['features']);
                    }
                    
                    // Add features to the product
                    //$planService->addFeaturesToPlan($plan,$product,$validated['features']);
                    app(FeatureService::class)->addFeaturesToProduct($plan,$product,$validated['features']);
                });
        } 
        catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating meeting room: ' . $e->getMessage());
        }

        return redirect()->route('admin.meeting-rooms.index')
                            ->with('success', 'Meeting Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $meeting_room)
    {
        $meeting_room->load(['features', 'media']);
        $productType = $this->productType;
        $featureSettings = FeatureSetting::all();
        return view('admin.meeting-rooms.edit', 
            ['meetingRoom'=>$meeting_room, 
            'productType' => $productType, 
            'featureSettings' => $featureSettings
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMeetingRoomRequest $request, Product $meeting_room, 
        ProductService $productService, FeatureService $featureService
    )
    { //dd($request->all(), $request->validated());
        try {
            \DB::transaction(function () use ($request, $meeting_room, $productService, $featureService) {
                $productData = $request->validated();
                
                if ($request->hasFile('main_product_image')) {
                    $productData['main_image'] = $request->file('main_product_image');
                }

                if ($request->hasFile('additional_images')) {
                    $productData['additional_images'] = $request->file('additional_images');
                }
                
                if ($request->input('existing_additional_images')) {
                    $productData['existing_additional_images'] = $request->input('existing_additional_images');
                }
                $productData['type'] =  $this->productType->value;
                // Update product, 
                $productService->updateProduct($meeting_room, $productData, $this->storageInfo);
                
                //get default meeting room and conference room plan
                $plan = Plan::where('slug', Str::slug(ProductTypeEnum::MEETING_ROOM->label()))->first();
                //update features
                $featureService->updateFeaturesForProduct($plan, $meeting_room, $request->input('features'));
            });

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating virtual address: ' . $e->getMessage());
        }
        return redirect()->route('admin.meeting-rooms.index')
                ->with('success', 'Meeting Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $meeting_room, ProductService $productService)
    {
        try{
            \DB::transaction(function () use ($meeting_room, $productService) {
                $productService->deleteProduct($meeting_room);
            });
        }catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating meeting room: ' . $e->getMessage());
        }
        return redirect()->route('admin.meeting-rooms.index')
                            ->with('success', 'Meeting Room deleted successfully.');
    }
}
