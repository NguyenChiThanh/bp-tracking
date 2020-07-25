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
}
