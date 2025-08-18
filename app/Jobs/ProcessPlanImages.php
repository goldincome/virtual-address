<?php

namespace App\Jobs;

use App\Models\Plan;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ProcessPlanImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Plan $plan,
        public ?UploadedFile $mainImage,
        public array $additionalImages
    ) {}

    public function handle(): void
    {
        try {
            // Handle main image
            if ($this->mainImage) {
                $this->plan->addMedia($this->mainImage)
                    ->toMediaCollection('plan_primary_image');
            }

            // Handle additional images
            foreach ($this->additionalImages as $image) {
                $this->plan->addMedia($image)
                    ->toMediaCollection('plan_additional_images');
            }
        } catch (FileDoesNotExist | FileIsTooBig $e) {
            logger()->error('Media processing failed: ' . $e->getMessage());
        }
    }
}