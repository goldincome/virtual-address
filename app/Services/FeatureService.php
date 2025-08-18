<?php
namespace App\Services;

use App\Models\{Plan};
use App\Models\Product;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class FeatureService
{

     public function addFeaturesToProduct(Plan $plan, Product $product, array $features): void
    {
        //dd($name, $features);
        foreach ($features as $index => $feature) {
            $product->features()->create([
                'description' => $feature['description'] ?? null,
                'is_activated' => $feature['is_active'],
                'plan_id' => $plan->id,
                'feature_setting_id' => $feature['icon_class'],
            ]);
        }
    }
    
    public function updateFeaturesForProduct(Plan $plan, Product $product, array $features): void
    {
        // Delete existing features
        $product->features()->each(function ($feature) {
            $feature->forceDelete();
        });

        // Create new features
        foreach ($features as $index => $feature) {
            $product->features()->create([
                'description' => $feature['description'] ?? null,
                'is_activated' => $feature['is_active'],
                //'product_id' => $product->id,
                'plan_id' => $plan->id,
                'feature_setting_id' => $feature['icon_class'],
            ]);
        }

    }

    // delete Features
    public function deleteFeatures(Product $product):void
    {
            // Delete plan images and data using MediaLibrary's methods
        $product->media()->each(function (Media $media) {
            $media->delete(); // This deletes both DB record and files
        });
            // Delete features
        $product->features()->each(function ($feature) {
            $feature->forceDelete();
        });

    }

}