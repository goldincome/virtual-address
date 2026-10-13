<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill existing users with a unique random 8 digit customer number.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('customer_number')
            ->select('id')
            ->chunkById(500, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['customer_number' => $this->generateUniqueNumber()]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->update(['customer_number' => null]);
    }

    /**
     * Generate a random 8 digit number that is not already used.
     */
    protected function generateUniqueNumber(): string
    {
        do {
            $number = (string) random_int(10000000, 99999999);
        } while (DB::table('users')->where('customer_number', $number)->exists());

        return $number;
    }
};
