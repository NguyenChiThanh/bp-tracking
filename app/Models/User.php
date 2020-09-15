<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable // implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    const PMC_USER = "pmc_user";
    const PARTNER_USER = "partner_user";

    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'cellphone',
        'company_id',
        'type',
        'status',
        'password',
        'access_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'is_admin',
        'is_mod',
        'is_pmc_user',
        'is_partner_user',
        'brands'
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '.jpg?s=200&d=mm';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Assigning User role
     *
     * @param \App\Models\Role $role
     */
    public function assignRole(Role $role)
    {
        return $this->roles()->save($role);
    }

    public function isAdmin()
    {
        return $this->roles()->where('name', 'Admin')->exists();
    }

    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }

    public function isMod()
    {
        return $this->isAdmin() || $this->roles()->where('name', 'Mod')->exists();
    }

    public function getIsModAttribute()
    {
        return $this->isMod();
    }

    public function isPMCUser()
    {
        return $this->isAdmin() || $this->isMod() || $this->roles()->where('name', 'PMC User')->exists();
    }

    public function getIsPMCUserAttribute()
    {
        return $this->isPMCUser();
    }

    public function isPartnerUser()
    {
        return $this->roles()->where('name', 'Partner User')->exists();
    }

    public function getIsPartnerUserAttribute()
    {
        return $this->isPartnerUser();
    }

    public function getBrandsAttribute() {
        $brands = $this->brands()->get();

        $rs = [];
        foreach ($brands as  $brand) {
            $rs += [
                'id' => $brand->id,
                'name' => $brand->name
            ];
        }

        return $rs;
    }

}
