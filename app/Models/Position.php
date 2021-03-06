<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    const AVAILABLE = 'AVAILABLE';
    const RESERVED = 'RESERVED';
    const RUNNING = 'RUNNING';

    protected $fillable = [
        'name',
        'description',
        'status',
        'width',
        'height',
        'image_url',
        'store_id',
        'channel',
        'buffer_days',
        'unit',
        'price',
    ];

    public function store()
    {
        return $this->belongsTo('App\Models\Store');
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\Booking');
    }
}
