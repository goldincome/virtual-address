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

class ConferenceRoomController extends Controller
{
    protected $productType = ProductTypeEnum::CONFERENCE_ROOM;
    protected $storageInfo = [
        'primary_image' => Product::CONFERENCE_PRIMARY_IMAGE,
        'additional_images' =>Product::CONFERENCE_ADDITIONAL_IMAGES,
        'image_folder' => Product::CONFERENCE_IMAGE_FOLDER
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conferenceRooms = Product::where('type', ProductTypeEnum::CONFERENCE_ROOM)
            ->with(['features', 'media'])
            ->latest()->paginate(10);
        //foreach($conferenceRooms as $meetingRoom){
          //  dd($meetingRoom->media);
        //}
        return view('admin.conference-rooms.index', compact('conferenceRooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $featureSettings = FeatureSetting::all();
        return view('admin.conference-rooms.create', 
            [ 'productType' =>$this->productType, 'featureSettings' => $featureSettings]);
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

                    $plan = Plan::where('slug', Str::slug(ProductTypeEnum::CONFERENCE_ROOM->label()))->first();

                    if(!$plan){
                        $planData = [
                            'name' => ProductTypeEnum::CONFERENCE_ROOM->label(),
                            'is_active' => false,
                            'price' => 0.00
                        ];
                        $plan = $planService->createPlanForProduct($product, $planData, $validated['features']);
                    }
                    //$planService->addFeaturesToPlan($plan,$product,$validated['features']);
                    app(FeatureService::class)->addFeaturesToProduct($plan,$product,$validated['features']);
                });
        } 
        catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating meeting room: ' . $e->getMessage());
        }

        return redirect()->route('admin.conference-rooms.index')
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
    public function edit(Product $conference_room)
    {
       $conference_room->load(['features', 'media']);
        $productType = $this->productType;
        $featureSettings = FeatureSetting::all();
        return view('admin.conference-rooms.edit', ['conferenceRoom'=>$conference_room, 
        'productType' => $productType, 'featureSettings' => $featureSettings]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMeetingRoomRequest $request, Product $conference_room, 
        ProductService $productService, FeatureService $featureService
    )
    { //dd($request->all(), $conference_room);
        try {
            \DB::transaction(function () use ($request, $conference_room, $productService, $featureService) {
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
                $productService->updateProduct($conference_room, $productData, $this->storageInfo);
                //get default meeting room and conference room plan
                $plan = Plan::where('slug', Str::slug(ProductTypeEnum::CONFERENCE_ROOM->label()))->first();
                //dd($plan, $conference_room, $request->input('features'));
                //update features
                $featureService->updateFeaturesForProduct($plan, $conference_room, $request->input('features'));
            });

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating virtual address: ');
        }
        return redirect()->route('admin.conference-rooms.index')
                ->with('success', 'Meeting Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $conference_room, ProductService $productService)
    {
        try{
            \DB::transaction(function () use ($conference_room, $productService) {
                $productService->deleteProduct($conference_room);
            });
        }catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating meeting room: ' . $e->getMessage());
        }
        return redirect()->route('admin.conference-rooms.index')
                            ->with('success', 'Meeting Room deleted successfully.');
    }
}
