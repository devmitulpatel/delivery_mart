<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SMSandNotificationController extends Controller
{
    //send sms
    public function sendUpdate(Request $request,$order_id)
    {
        
        $data = \DB::table('orders')
                    ->where('id','=',$order_id)
                    ->get()
                    ->first();
        
        $status = $data->status;
        $message = "Dear Customer,".$data->recipient_name.", DBJM Sity Mart are happy to be of service. Your order is ".$status.". Selected mode of payment is ".$data->payment_mode.".Your verification code is ".$data->verification_code.". Check App for more details.";
        $response = $this->sendSms($data->recipient_phone,$message);

        $title = "Order Update";
        $fcm_token = \DB::table('users')->where('id',$data->user_id)->value('notification_Token');
        if($fcm_token !== null){
            $notification_response = $this->sendPushNotification($fcm_token, $title, $message, $id = null);  
        }      
        //return $response;
        return redirect('/admin/orders')->with(['message'=>$response,'alert-type'=>'success']);
        
    }
    //notification
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
