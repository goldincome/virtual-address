<?php

use App\Enums\OrderStatusEnum;
use App\Enums\ProductTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default(ProductTypeEnum::VIRTUAL_ADDRESS->value);
            $table->string('intro')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->string('currency')->default(config('cashier.currency'));
            $table->string('main_product_image')->nullable();
            $table->timestamps();
        });
        

        // Add product_id to plans table (assuming package's table exists)
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }

};
