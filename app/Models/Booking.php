<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $fillable = [
        'campaign_id',
        'position_id',
        'from_ts',
        'to_ts',
        'buffer_ts'
    ];
}
