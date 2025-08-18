<?php

namespace Database\Seeders;

use App\Models\OpeningDay;
use Illuminate\Database\Seeder;

class OpeningDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $data = [];
            foreach($days as $day){
                $data['day_name'] =  $day;
                $data['open_time'] =  '00:00:00';
                $data['close_time'] =  '09:00:00';
                $data['status'] = true;
                OpeningDay::firstOrCreate($data);

            }
    }
}
