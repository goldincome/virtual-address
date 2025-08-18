<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
  protected $fillable = [
        'plan_id',
        'description',
        'sort_order',
        'product_id',
        'is_activated',
        'feature_setting_id',
    ];
   
    public function product()
    {
        return $this->belongsTo(Product::class);
    }



    public function featureSetting()
    {
        return $this->belongsTo(FeatureSetting::class, 'feature_setting_id');
    }

}
