<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningDay extends Model
{
    protected $fillable = [
        'day_name', 'open_time', 'close_time', 'status' ];

    

}
