<?php

namespace App\Http\Controllers;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Order;
class OrderController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
   public function assign(){
    $order = Order::where('id', \request("id"))->first();
    $order->assign = $order->assign == "ASSIGN NOW" ? "ASSIGNED" : "ASSIGN NOW";
    $order->save();
    return response()->json($order);

   } 
}