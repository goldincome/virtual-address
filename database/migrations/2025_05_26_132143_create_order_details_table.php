<?php

use App\Enums\OrderStatusEnum;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->string('ref_no')->unique();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('product_id')->constrained();
             $table->foreignId('plan_id')->nullable()->constrained();
            $table->string('product_type');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->string('status')->default(OrderStatusEnum::PENDING->value);
            $table->dateTime('booked_date')->nullable();
            $table->text('all_booked_time')->nullable();
            $table->text('plan')->nullable();
            $table->text('features')->nullable();
            $table->text('discounts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};

