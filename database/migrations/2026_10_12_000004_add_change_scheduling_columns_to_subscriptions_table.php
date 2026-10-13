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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('subscription_schedule_id')->nullable()->after('ending_at');
            $table->unsignedBigInteger('scheduled_plan_id')->nullable()->after('subscription_schedule_id');
            $table->dateTime('scheduled_change_at')->nullable()->after('scheduled_plan_id');
            $table->string('scheduled_change_type')->nullable()->after('scheduled_change_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_schedule_id',
                'scheduled_plan_id',
                'scheduled_change_at',
                'scheduled_change_type',
            ]);
        });
    }
};
