<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['id', 'name', 'products', 'company_id'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
