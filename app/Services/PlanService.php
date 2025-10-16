<?php
namespace App\Services;

use App\Models\{Plan};
use App\Models\Product;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class PlanService
{

    public function createPlanForProduct(Product $product, array $planData, array $features): Plan
    {                     
        $plan = $product->plans()->create([
            'name' => $planData['name'],
            'slug' => Str::slug($planData['name']),
            'description' => $planData['description'] ?? null,
            'price' => $planData['price'],
            'yearly_monthly_price' => $planData['yearly_monthly_price'] ?? 0.00,
            'invoice_period' => $planData['invoice_period'] ?? 1,
            'is_active' => $planData['is_active'] ?? false,
            'level' => $planData['level'] ?? 0,
            'invoice_interval' => 'month',
            'discount_duration_in_months' => $planData['discount_duration_in_months'] ?? null,
            'discount_amount' => $planData['discount_amount'] ?? 0.00,
            'discount_percent' => $planData['discount_percent'] ?? 0.00,
            'currency' => config('cashier.currency'),
            // ... other fields
        ]);
        if($product->type->value === ProductTypeEnum::VIRTUAL_ADDRESS->value){
            // Primary image
            if (isset($planData['main_plan_image'])) {
            $this->addPrimaryImage($plan, $planData['main_plan_image']);
            }
            //Other images
            if (isset($planData['additional_images'])) {
                $this->addImages($plan, $planData['additional_images']);
            }
       
            //add features to plan
            $this->addFeaturesToPlan($plan, $product, $features);
            $product->update(['is_complete'=> true]);
        }
        
        return $plan;
    }

    //Add additional images to plan
    public function addImages(Plan $plan, array $images): void
    {
        collect($images)->each(function (UploadedFile $image) use ($plan) {
            $plan->addMedia($image)->toMediaCollection($plan::ADDITIONAL_IMAGES, $plan::IMAGE_FOLDER);
        });
    }

    //Add main/primary image to plan
    public function addPrimaryImage(Plan $plan, UploadedFile $image)
    {
        try {
            $plan->addMedia($image)->toMediaCollection($plan::PRIMARY_IMAGE, $plan::IMAGE_FOLDER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addFeaturesToPlan(Plan $plan, Product $product, array $features): void
    {
        foreach ($features as $feature) {
            //$name = $plan->features()->count().str_pad(rand(1, 50000), 5, "0", STR_PAD_LEFT);
            $plan->features()->create([
                'feature_setting_id' => $feature['icon_class'],
                'description' => $feature['description'] ?? null,
                'is_activated' => $feature['is_active'],
                'product_id' => $product->id,
            ]);
        }
    }

    // Add these methods to PlanService
    public function updatePlan(Plan $plan, array $planData, array $features): Plan
    {   //dd($planData, $features);
        try{
       
            $plan->update([
                'name' => $planData['name'],
                'description' => $planData['description'] ?? null,
                'price' => $planData['price'] ?? $plan->price,
                'yearly_monthly_price' => $planData['yearly_monthly_price'] ?? $plan->yearly_monthly_price,
                'discount_duration_in_months' => $planData['discount_duration_in_months'] ?? null,
                'discount_amount' => $planData['discount_amount'] ?? null,
                'discount_percent' => $planData['discount_percent'] ?? null,
                'is_active' => $planData['is_active'] ?? false,
                'level' => $planData['level'] ?? 0,
            ]);

            // Handle main image update
            if (isset($planData['main_plan_image'])) {
                $plan->clearMediaCollection($plan::PRIMARY_IMAGE);
                $this->addPrimaryImage($plan, $planData['main_plan_image']);
            }

            // Handle remove unselected additional images
            if (isset($planData['existing_additional_images'])) {
                $plan->getMedia($plan::ADDITIONAL_IMAGES)->whereNotIn('uuid', $planData['existing_additional_images'])
                    ->each(function($image){
                    $image->delete();
                });       
            }

            // Handle additional images
            if (isset($planData['additional_images'])) {
                $this->addImages($plan, $planData['additional_images']);
            }

            // Update features
            $this->updateFeatures($plan, $features);

            return $plan->refresh();
        } 
        catch (\Exception $e) {
            throw new \Exception('Error updating plan: ' . $e->getMessage());
        }
    }

private function updateFeatures(Plan $plan, array $features): void
{
    // Delete existing features
    $plan->features()->each(function ($feature) {
        
        $feature->forceDelete();
    });

    // Update features
    foreach ($features as $feature) {
       // $name = $plan->features()->count().str_pad(rand(1, 50000), 5, "0", STR_PAD_LEFT);
        $plan->features()->create([
            'feature_setting_id' => $feature['icon_class'],
            'description' => $feature['description'] ?? null,
            'is_activated' => $feature['is_active'],
            'product_id' => $plan->product_id,
        ]);
    }
        
}

    // In PlanService
    public function deletePlan(Plan $plan): void
    {
        // Delete plan images and data using MediaLibrary's methods
        $plan->media()->each(function (Media $media) {
            $media->delete(); // This deletes both DB record and files
        });
        // Delete features
        $plan->features()->each(function ($feature) {
            $feature->forceDelete();
        });

        // Delete plan itself
        $plan->forceDelete();
        // Delete the product
    }

}