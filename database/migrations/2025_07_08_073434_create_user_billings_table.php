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
        Schema::create('user_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('company_name');
            $table->string('billing_address');
            $table->string('postal_code');
            $table->string('vat_no')->nullable();
            $table->string('acct_holder_name')->nullable();
            $table->string('acct_number')->nullable();
           // $table->string('bank_name');
            $table->string('sort_code')->nullable();
            $table->boolean('approval_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_billings');
    }
};
