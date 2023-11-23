<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; // <-- import JWTSubject
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- import Auth Laravel

class Customer extends Authenticatable implements JWTSubject // <-- tambahkan "Authenticatable" dan "JWTSubject
{
    use HasFactory;
    protected $fillable =[
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected function cratedAt() : Attribute {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::locale('id')->parse($value)->translatedFormat('l, d F Y'),
        );
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims() {
        return [];
    }
}
