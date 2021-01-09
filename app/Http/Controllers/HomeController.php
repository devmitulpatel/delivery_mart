<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Storage;

class HomeController extends Controller
{
    
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    
    public function index()
    {
        return view('home');
    }
//delivery fee
    public function deliveryFeeModify(Request $request){
            Storage::disk('local')->put('deliveryFee.txt', $request->deliveryFee);
            return redirect('/admin')->with(['message'=>'Delivery fee changed','alert-type'=>'success']);
    }
//sendsms
   
}
