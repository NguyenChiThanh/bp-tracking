<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    const IN_OPERATING = 'IN_OPERATING'; //    Đang hoạt động
    const COMING_SOON = 'COMING_SOON'; //    Sắp khai trương
    const CLOSED = 'CLOSED'; //    Ngưng hoạt động

    const LEVEL_A = 'A';
    const LEVEL_B = 'B';
    const LEVEL_C = 'C';
    const LEVEL_D = 'D';

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'status',
        'level',
        'province_id',
        'province',
        'district_id',
        'district',
        'ward_id',
        'ward',
        'address',
        'images',
        'longitude',
        'latitude'
    ];

    public function positions()
    {
        return $this->hasMany('App\Models\Position');
    }
}
