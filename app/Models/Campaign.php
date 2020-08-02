<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'contract_code',
        'license_code',
//        'status',
        'brand_id',
        'from_ts',
        'to_ts',
        'discount_type',
        'discount_value',
        'discount_max',
        'total_discount',
        'total_price',
        'created_by',
        'position_list'
    ];


    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
}
