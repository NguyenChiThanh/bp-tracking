<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name'];

    protected $table = 'company';

    public function brands() {
        return $this->hasMany(Brand::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
