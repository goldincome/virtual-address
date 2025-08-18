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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('stripe_product_id')->nullable()->after('slug');
            $table->string('interval')->nullable()->after('stripe_product_id');
            // Add a column for the yearly price
            $table->decimal('yearly_monthly_price', 8, 2)->default('0.00')->after('price');
            $table->string( 'stripe_price_id_monthly')->nullable()->after('yearly_monthly_price');
            $table->string('stripe_price_id_yearly')->nullable()->after('stripe_price_id_monthly');
            // Fields to define the discount on your site
            $table->decimal('discount_percent', 5, 2)->default(0)->after('stripe_price_id_yearly')->comment('A percentage-based discount');
            $table->decimal('discount_amount', 8, 2)->default(0)->after('discount_percent')->comment('A flat amount discount');
            $table->unsignedInteger('discount_duration_in_months')->nullable()->after('discount_amount');

            // Fields to store the IDs from Stripe
            $table->string('stripe_coupon_id')->nullable()->after('stripe_price_id_yearly');
            $table->string('stripe_promotion_code_id')->nullable()->after('stripe_coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_product_id',
                'stripe_price_id',
                'interval',
                'yearly_monthly_price',
                'stripe_price_id_monthly',
                'stripe_price_id_yearly',
                'discount_percent',
                'discount_amount',
                'discount_duration_in_months',
                'stripe_coupon_id',
                'stripe_promotion_code_id',
            ]);
        });
    }
};
