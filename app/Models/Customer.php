<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable =['name', 'email','email_verified_at','password','remember_token'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getCreatedAtAttribute($date)
    {
        $value = Carbon::parse($date);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
