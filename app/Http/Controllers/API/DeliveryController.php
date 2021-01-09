<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \DB;
use App\Order;
use App\DeliveryPosition;
use \Storage;
use App\Actions\Textlocal;

class DeliveryController extends Controller
{
//orders
    public function getOrdersCountForDeliveryGuy(Request $request)
    {
        $user = $request->user()->id;
        $order_count = \DB::table('orders')->where('delivery_boy_id','=',$user)->count();
        return response()->json(['message'=>1,'totalOrdersCount'=>$order_count]);
    }

    public function getAllPendingOrders(Request $request)
    {       
        $user = $request->user()->id;
        $products = DB::table('orders')->where([['delivery_boy_id',$user],['status','=','Pending']])->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }

    public function getAllProcessingOrders(Request $request)
    {
        $user = $request->user()->id; 
        $products = DB::table('orders')->where([['delivery_boy_id',$user],['status','=','Processing']])->get();
        return response()->json(['message'=>1,'data'=>$products]);      
    }
    public function getAllCompletedOrders(Request $request)
    {
        $user = $request->user()->id;
        $products = DB::table('orders')->where([['delivery_boy_id',$user],['status','=','Completed']])->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }
//order status change
    public function changeStatusOfOrder(Request $request)
    {
        $user = $request->user()->id;
        $order = Order::findorFail($request->orderId);
        if($order === null){
            return response()->json(['message'=>0]);
        }
        else{
            $order->status = $request->status;
            $order->save();

//send sms
        $message = "Dear Customer,".$order->recipient_name.", DBJM Sity Mart are happy to be of service. Your order is ".$order->status.". Selected mode of payment is ".$order->payment_mode.".Your verification code is ".$order->verification_code.". Check App for more details.";
        $response = $this->sendSms($order->recipient_phone,$message);

        $title = "Order Update";
        $fcm_token = \DB::table('users')->where('id',$order->user_id)->value('notification_Token');
        if($fcm_token !== null){
            $notification_response = $this->sendPushNotification($fcm_token, $title, $message, $id = null);  
        }      


            return response()->json(['message'=>1]);
        }
    }
//delivery location
    public function updateDeliveryLocation(Request $request)
    {
        $user = $request->user()->id;
        $delivery_boy = \App\DeliveryPosition::where('order_id','=',$request->orderId)->firstorFail();
        if ($delivery_boy === null)
        {
            return ['message'=>0];
        }
        else{
            $delivery_boy->lat = $request->lat;
            $delivery_boy->lng = $request->lng;
            $delivery_boy->save();
            return response()->json(['message'=>1]);
        }
    }
//code verify
    public function verifyCode(Request $request)
    {
        $code = DB::table('orders')->where('id',$request->orderId)->get('verification_code');
        // return $code;
        if($code[0]->verification_code == $request->code){
            return response()->json(['message'=>1]);
        }
        else{
            return response()->json(['message'=>0]);
        }
    }
//signature upload
    public function uploadSignature(Request $request)
    {
        //store signature image
        $image = $request->signature;
        $ext = explode(';base64',$image);
        $ext = explode('/',$ext[0]);
        $ext = $ext[1];
        $replace = substr($image,0,strpos($image,',')+1);
        $image = str_replace($replace,'',$image);
        $image = str_replace('','+',$image);
        $imageName = \Str::random(10).'.jpg';
        \Storage::disk('public')->put('orders/'.$imageName,base64_decode($image));
        $order = Order::find($request->orderId);
        $order->signature_customer = 'orders/'.$imageName;

        //store parcel image
        $package = $request->package;
        $ext = explode(';base64',$package);
        $ext = explode('/',$ext[0]);
        $ext = $ext[1];
        $replace = substr($package,0,strpos($package,',')+1);
        $package = str_replace($replace,'',$package);
        $package = str_replace('','+',$package);
        $packageName = \Str::random(10).'.jpg';
        \Storage::disk('public')->put('packages/'.$packageName,base64_decode($package));
        $order->package = 'packages/'.$packageName;

        $order->save();
        return response()->json(['message'=>1]);
    }
    //delivery fee
    public function deliveryFee(){
        if (Storage::exists('deliveryFee.txt')){
            $contents = Storage::get('deliveryFee.txt');
            return response()->json(['data'=>$contents]);
        }
    }
    public function sendPushNotification($fcm_token, $title, $message, $id = null) {  
        $your_project_id_as_key = 'AAAAYd1_VkQ:APA91bGr30YrLfwqDdVmd8f7oTQbhXp6Aa_dl2mcFwTZGkafFxZqGok63pgZn9I0pezwja1kUVpyhSYysRdt_-kwRvIuZuUuy2gcHZG4PzQLvZKBwuupLnJm6jCsPZEoItdeBSWMSJud';
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = [
        'authorization: key=' . $your_project_id_as_key,
            'content-type: application/json'
        ];    

        $postdata = '{
            "to" : "' . $fcm_token . '",
                "notification" : {
                    "title":"' . $title . '",
                    "text" : "' . $message . '"
                },
            "data" : {
                "id" : "'.$id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);    
        curl_close($ch);

        return $result;
    }
    //send sms
    public function sendSms($mobile,$message)
    {   
        
        $curl = curl_init();    
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://message.rajeshwersoftsolution.com/rest/services/sendSMS/sendGroupSms?AUTH_KEY=bdf5c4d0f53a946e2cbc4a9491cfeab3",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"smsContent\":\".$message.\",\"routeId\":\"8\",\"mobileNumbers\":\".$mobile.\",\"senderId\":\"DEMOOS\",\"signature\":\"DBJM SITY MART\",\"smsContentType\":\"english\"}",
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
        return "cURL Error #:" . $err;
        } else {
        return $response;
        }
    }
}
