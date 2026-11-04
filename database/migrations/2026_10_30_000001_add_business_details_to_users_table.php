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
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_type')->nullable()->after('status');
            $table->string('business_registration_name')->nullable()->after('business_type');
            $table->string('business_trading_name')->nullable()->after('business_registration_name');
            $table->string('business_registration_number')->nullable()->after('business_trading_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'business_registration_name', 'business_trading_name', 'business_registration_number']);
        });
    }
};
