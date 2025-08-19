<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear the table before seeding to avoid duplicate entries

        DB::table('users')->insert([
            [
                'name' => 'Nolan Finch',
                'email' => 'obiora@mailinator.com',
                'first_name' => null,
                'last_name' => null,
                'phone' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$6eagp0To9Wna7Lji/7IkQ.U1rMv5QIlc.p3EVfk1E8cepcMwKyxKi', // It's better to use Hash::make('password') for new seeders
                'remember_token' => 'KpHU04Z04OHO9C90z3Sv6COyx31syU6UQUBwNvLktAP7B5TMjdkJSVN4Zb18',
                'created_at' => '2025-05-12 15:04:29',
                'updated_at' => '2025-06-21 00:18:35',
                'user_type' => 'admin',
                'stripe_id' => 'cus_SXKk6slJOwEgkB',
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ],
            [
                'name' => 'Yoshi Ross',
                'email' => 'ifylovely@mailinator.com',
                'first_name' => null,
                'last_name' => null,
                'phone' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$6eagp0To9Wna7Lji/7IkQ.U1rMv5QIlc.p3EVfk1E8cepcMwKyxKi',
                'remember_token' => null,
                'created_at' => '2025-05-28 10:01:53',
                'updated_at' => '2025-06-20 22:07:06',
                'user_type' => 'super_admin',
                'stripe_id' => 'cus_SXIcoOkOfJEEYq',
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ],
            [
                'id' => 4,
                'name' => 'Preston Bridges',
                'email' => 'rilox@mailinator.com',
                'first_name' => null,
                'last_name' => null,
                'phone' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$j6CFqVmySB8UL6B2dYihl.PRQJhFk5NrFtBlD95W.lBLc0tlbC.e.',
                'remember_token' => null,
                'created_at' => '2025-06-21 08:46:24',
                'updated_at' => '2025-06-21 09:11:06',
                'user_type' => 'customer',
                'stripe_id' => 'cus_SXTK0y8uOpSmK6',
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ],
            [
                'name' => 'Shoshana Dale',
                'email' => 'beme@mailinator.com',
                'first_name' => 'Shoshana',
                'last_name' => 'Dale',
                'phone' => '+1 (451) 329-8006',
                'email_verified_at' => null,
                'password' => '$2y$12$4be10wOehJojZVCctFdmguZsMUSqdvKuqHFHcBjq//3yWS.Leynl2',
                'remember_token' => null,
                'created_at' => '2025-07-29 12:15:03',
                'updated_at' => '2025-07-29 12:16:16',
                'user_type' => 'customer',
                'stripe_id' => 'cus_SlktLVqPqOLf7x',
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ],
            [

                'name' => 'Jamalia Oliver',
                'email' => 'oliver@mailinator.com',
                'first_name' => 'Jamalia',
                'last_name' => 'Oliver',
                'phone' => '+1 (362) 684-6242',
                'email_verified_at' => null,
                'password' => '$2y$12$x39kUFIHZZ3h1t7KSvVgEuB8mAOfCitc4.mi5zgaW9yr937prz4Yy',
                'remember_token' => null,
                'created_at' => '2025-08-02 11:17:46',
                'updated_at' => '2025-08-02 11:18:52',
                'user_type' => 'customer',
                'stripe_id' => 'cus_SnEsynjrVNOzC2',
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ],
            [
                'name' => 'Xyla Herrera',
                'email' => 'vily@mailinator.com',
                'first_name' => 'Xyla',
                'last_name' => 'Herrera',
                'phone' => '+1 (709) 876-2715',
                'email_verified_at' => null,
                'password' => '$2y$12$MmqCf2KRAAx62UM5Ds.jyOMfACc7X7UHUZSP1Mo99vR/WSGQSaCGq',
                'remember_token' => null,
                'created_at' => '2025-08-04 13:12:14',
                'updated_at' => '2025-08-04 13:19:57',
                'user_type' => 'customer',
                'stripe_id' => 'cus_So1HKsfjAJGbIX',
                'pm_type' => null,
                'pm_last_four' => null,
                'trial_ends_at' => null,
            ],
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

 
