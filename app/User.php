<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable,HasApiTokens;

    public function scopeDelivery($query){
        return $query->where('role_id','=',2);
    }
    public function scopeCustomer($query){
        return $query->where('role_id','=',3);
    }
    public function scopePartner($query){
        return $query->where('role_id','=',4);
    }
    
    protected $fillable = [
        'name', 'email', 'password', 'phone','role_id','notification_Token','vehicle_number','address'
    ];

    
    protected $hidden = [
        'password', 'remember_token',
    ];

    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
