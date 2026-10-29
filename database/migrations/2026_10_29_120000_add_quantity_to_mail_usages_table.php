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
        Schema::table('mail_usages', function (Blueprint $table) {
            if (!Schema::hasColumn('mail_usages', 'quantity')) {
                $table->integer('quantity')->default(1)->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_usages', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
