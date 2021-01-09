<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
        'user_id','products','delivery_boy_id','status','signature_customer','address','type','recipient_name','recipient_phone','total_amount','delivery_amount','lat','lng','package'
    ];
}
