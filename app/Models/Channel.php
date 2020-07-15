<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    const ACTIVE = 'ACTIVE';
    const INACTIVE = 'INACTIVE';

    protected $fillable = ['name', 'image_url', 'status'];
}
