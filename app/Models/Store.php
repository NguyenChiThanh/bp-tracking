<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    const IN_OPERATING = 'IN_OPERATING'; //    Đang hoạt động
    const COMING_SOON = 'COMING_SOON'; //    Sắp khai trương
    const CLOSED = 'CLOSED'; //    Ngưng hoạt động

    protected $fillable = [
        'name', 'description', 'status'
    ];

}
