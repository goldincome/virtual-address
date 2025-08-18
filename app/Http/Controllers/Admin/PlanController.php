<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Product;
use App\Services\PlanService;
use App\Enums\ProductTypeEnum;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Http\Requests\VirtualAddresses\StoreVirtualAddressRequest;
use App\Http\Requests\VirtualAddresses\UpdateVirtualAddressRequest;
use App\Models\FeatureSetting;

class PlanController extends Controller
{ 
    protected $productType = ProductTypeEnum::VIRTUAL_ADDRESS;

    public function index()
    {
        $product = Product::where('type', ProductTypeEnum::VIRTUAL_ADDRESS)->first();
        $plans = null;        
        if($product){
            $plans = $product->plans()->with(['features', 'media'])->latest()->paginate(10);
        }
        return view('admin.plans.index', compact('product', 'plans'));
    }

    public function create()
    { 
        $featureSettings = FeatureSetting::all();
        return view('admin.plans.create', 
            [ 'productType' =>$this->productType, 'featureSettings' => $featureSettings]);
    }

    public function store(StoreVirtualAddressRequest $request, ProductService $productService, PlanService $planService)
    {
        $validated = $request->validated();
        //dd($validated);
        try
        {
               // \DB::transaction(function () use ($request, $validated, $productService, $planService) {
                    $product = Product::where('type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->first();
                    $folderName = ['image_folder' => Plan::IMAGE_FOLDER];
                    if(!$product){
                        $dataProduct = [
                            'name' => ProductTypeEnum::VIRTUAL_ADDRESS->label(),
                            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value
                        ];
                        $product = $productService->createProduct($dataProduct, null, $folderName);
                    }
                    $planData = [];
                    $planData['discount_duration_in_months'] = 10;
                    $planData['yearly_monthly_price'] = $request->price * 10;
                    $planData['discount_amount'] = ($request->price * 12) - $planData['yearly_monthly_price'];
                    $planData['discount_percent'] =(float) number_format(($planData['discount_amount']/($request->price * 12)) * 100, 2);
                    //dd($planData);
                    if ($request->hasFile('main_product_image')) {
                        $planData['main_plan_image'] =  $request->file('main_product_image') ?? null;
                    }

                    // Store additional images
                    if ($request->hasFile('additional_images')) {
                        $planData['additional_images'] =  $request->file('additional_images') ?? null;
                    }

                    // Create plans with features
                    $planService->createPlanForProduct(
                        $product,
                        array_merge($request->only(['name', 'description', 'price',  'is_active', 'level']), $planData),
                        $validated['features']
                    );
               // });

        } 
        catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating virtual address: ' . $e->getMessage());
        }

        return redirect()->route('admin.plans.index')
                            ->with('success', 'Virtual Address created successfully.');
    }

    public function show(Plan $plan)
    { 
        if ($plan->product->type !== $this->productType) {
            abort(404);
        }
        $plan->load(['features', 'media']);
        return view('admin.plans.show', compact('plan'));
        
    }

    public function edit(Plan $plan)
    {
        $plan->load(['features', 'media']);
        $productType = $this->productType;
        $featureSettings = FeatureSetting::all();
        return view('admin.plans.edit', compact('plan', 'productType', 'featureSettings'));
    }

    public function update(UpdateVirtualAddressRequest $request, Plan $plan, PlanService $planService)
    {   
        try {
            \DB::transaction(function () use ($request, $plan, $planService) {
                $planData = $request->only(['name', 'description', 'price', 'is_active', 'level']);
                    $planData['discount_duration_in_months'] = 10;
                    $planData['yearly_monthly_price'] = $request->price * 10;
                    $planData['discount_amount'] = ($request->price * 12) - $planData['yearly_monthly_price'];
                    $planData['discount_percent'] =(float) number_format(($planData['discount_amount']/($request->price * 12)) * 100, 2);
                if ($request->hasFile('main_product_image')) {
                    $planData['main_plan_image'] = $request->file('main_product_image');
                }

                if ($request->hasFile('additional_images')) {
                    $planData['additional_images'] = $request->file('additional_images');
                }
                
                if ($request->input('existing_additional_images')) {
                    $planData['existing_additional_images'] = $request->input('existing_additional_images');
                }
                //dd($planData, $request->input('features'));
                // Update plan
                $planService->updatePlan($plan, $planData, $request->input('features'));
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating virtual address: ' . $e->getMessage());
        }
        return redirect()->route('admin.plans.index')
                ->with('success', 'Virtual Address updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        try {
            \DB::transaction(function () use ($plan) {
                if($plan->orderDetails()->exists()) {
                    return redirect()->back()
                        ->with('error', 'Cannot delete virtual address plan with existing subscription.');
                }
                app(PlanService::class)->deletePlan($plan);
            });
        } 
        catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting virtual address: '.$e->getMessage());
        }
                
        return redirect()->route('admin.plans.index')
                ->with('success', 'Virtual Address deleted successfully.');
    }
}


