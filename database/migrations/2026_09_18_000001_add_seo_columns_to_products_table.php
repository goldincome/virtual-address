<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedSmallInteger('capacity')->nullable()->after('price');
            $table->json('amenities')->nullable()->after('capacity');
        });

        DB::table('products')->where('slug', 'the-focus-room')->update([
            'capacity' => 4,
            'amenities' => json_encode([
                'High-speed Wi-Fi',
                '4K Display Screen',
                'Video Conferencing',
                'Whiteboard',
                'Ergonomic Chairs',
                'Tea & Coffee',
            ]),
        ]);

        DB::table('products')->where('slug', 'the-boardroom')->update([
            'capacity' => 12,
            'amenities' => json_encode([
                'High-speed Wi-Fi',
                'Large 4K Display',
                'Video Conferencing',
                'Flipchart / Whiteboard',
                'Boardroom Table',
                'PA System',
                'Tea & Coffee',
            ]),
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'amenities']);
        });
    }
};