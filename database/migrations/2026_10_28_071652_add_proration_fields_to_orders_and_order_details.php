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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_invoice_id')->nullable()->after('payment_method_order_id');
            $table->decimal('prorated_sub_total', 10, 2)->nullable()->after('sub_total');
            $table->decimal('prorated_tax', 10, 2)->nullable()->default(0)->after('tax');
            $table->decimal('prorated_discount', 10, 2)->nullable()->default(0)->after('discount');
            $table->decimal('prorated_total', 10, 2)->nullable()->after('total');
            $table->json('proration_breakdown')->nullable()->after('prorated_total');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
            $table->decimal('original_sub_total', 10, 2)->nullable()->after('sub_total');
            $table->decimal('prorated_price', 10, 2)->nullable()->after('original_sub_total');
            $table->decimal('prorated_sub_total', 10, 2)->nullable()->after('prorated_price');
            $table->json('proration_line_meta')->nullable()->after('prorated_sub_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
                'original_price',
                'original_sub_total',
                'prorated_price',
                'prorated_sub_total',
                'proration_line_meta',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_invoice_id',
                'prorated_sub_total',
                'prorated_tax',
                'prorated_discount',
                'prorated_total',
                'proration_breakdown',
            ]);
        });
    }
};
