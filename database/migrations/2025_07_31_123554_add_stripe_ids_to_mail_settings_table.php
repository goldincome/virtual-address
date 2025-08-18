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
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->string('stripe_product_id')->nullable()->after('id');
            $table->string('name')->after('id');
            $table->string('stripe_price_name')->after('name');
            $table->string('stripe_price_id')->nullable()->after('stripe_product_id');
            $table->string('interval')->default('month')->after('stripe_price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->dropColumn('stripe_product_id');
            $table->dropColumn('stripe_price_id');
            $table->dropColumn('interval');
            $table->dropColumn('name');
            $table->dropColumn('stripe_price_name');
        });
    }
};
