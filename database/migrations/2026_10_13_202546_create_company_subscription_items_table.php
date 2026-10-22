<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_type_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_subscription_item_id')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('interval')->default('month');
            $table->timestamps();
            $table->unique(['user_id', 'company_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_subscription_items');
    }
};
