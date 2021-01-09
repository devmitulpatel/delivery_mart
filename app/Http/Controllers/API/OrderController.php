<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Http\Controllers\API\DB;
use Illuminate\Support\Facades\Auth;
use Validator;

class OrderController extends Controller
{
//login
    public function saveOrder(Request $request){ 
$data=[
        $signature_customer=$request,
        $address=$request,
        $payment_mode=$request,
        $recipient_name=$request,
        $recipient_phone=$request,
        $delivery_amount=$request,
        $total_amount=$request,
        $lat=$request,
        $lng=$request,
        $verification_code=$request,
        $delivery_date=$request,
        $package=$request,

];
        DB::table('orders')->insert($data);

    }

}
