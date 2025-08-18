<?php
namespace App\Services;

use App\Models\{Product};
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function createProduct(array $productData, ?array $features, array $storageInfo): Product
    { //dd($productData, $features, $folder_name);
        $product = Product::create([
            'name' => $productData['name'],
            'type' => $productData['type'],
            'description' => $productData['description'] ?? null,
            'intro' => $productData['intro'] ?? null,
            'price' => $productData['price'] ?? null,
            'is_active' => $productData['is_active'] ?? true,
        ]);
        //check if is vitual address or meeting room and conference room 
        if($product->type->value === ProductTypeEnum::MEETING_ROOM->value || 
            $product->type->value === ProductTypeEnum::CONFERENCE_ROOM->value){
            // Primary image
            if (isset($productData['main_image'])) {
                $this->addPrimaryImage($product, $productData['main_image'], $storageInfo);
            }
            //Other images
            if (isset($productData['additional_images'])) {
                $this->addImages($product, $productData['additional_images'], $storageInfo);
            }
            //app(FeatureService::class)->addFeaturesToProduct($product,$productData,$features, $storageInfo);
        }
        return $product;
    }

    //Add additional images to product
    public function addImages(Product $product, array $images, array $storageInfo): void
    {
        collect($images)->each(function (UploadedFile $image) use ($product, $storageInfo ) {
            $product->addMedia($image)->toMediaCollection(
                $storageInfo['additional_images'], 
                $storageInfo['image_folder']);
        });
    }

    //Add main/primary image to product
    public function addPrimaryImage(Product $product, UploadedFile $image,  array $storageInfo)
    {
        try {
            $product->addMedia($image)->toMediaCollection($storageInfo['primary_image'], $storageInfo['image_folder']);
        } catch (\Exception $e) {
            throw $e;
        }
    }

        // Update product of ProductService
    public function updateProduct(Product $product, array $productData,  array $storageInfo ): Product
    {
        $product->update([
            'name' => $productData['name'],
            'type' => $productData['type'],
            'description' => $productData['description'] ?? null,
            'intro' => $productData['intro'] ?? null,
            'price' => $productData['price'],
            'is_active' => $productData['is_active'] ?? true,
        ]);

        // Handle main image update
        if (isset($productData['main_image'])) {
            $product->clearMediaCollection($storageInfo['primary_image']);
            $this->addPrimaryImage($product, $productData['main_image'], $storageInfo);
        }
        // Handle remove unselected additional images
        if (isset($productData['existing_additional_images'])) {
            $product->getMedia($storageInfo['additional_images'])->whereNotIn('uuid', $productData['existing_additional_images'])
                ->each(function($image){
                $image->delete();
            });       
        }
        
        // Handle additional images
        if (isset($productData['additional_images'])) {
            $this->addImages($product, $productData['additional_images'], $storageInfo);
        }

        // Update features
        //app(FeatureService::class)->updateFeaturesForProduct($product, $features);

        return $product->refresh();
    }

    public function deleteProduct(Product $product)
    {
        try{
            app(FeatureService::class)->deleteFeatures($product);
            $product->forceDelete();

        }catch (\Exception $e) {
            throw $e;
            return false;
        } 
        return true;
    }
}
