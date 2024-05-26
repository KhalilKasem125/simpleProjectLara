<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Model implements JWTSubject,Authenticatable
{
    use HasFactory,AuthenticatableTrait;

    protected $table = "admins";
    protected $fillable =[
        'first_name',
        'last_name',
        "email",
        "phone_no",
        "role",
        "password"
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
