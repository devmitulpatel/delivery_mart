<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feedback;
use Validator;
use App\Order;
use App\Restaurant;
use App\Category;
use App\Coupon;
use App\Actions\Textlocal;

class DBController extends Controller
{
//restaurants
    public function restaurants(){
        $restaurants = \DB::table('restaurants')
                ->get(['id','name']);
        if(count($restaurants) == 0){
            return response()->json(['message'=>0]);
        }
        else{
            return response()->json(['message'=>1,'data'=>$restaurants]);
        }      
    }
//categories
    public function categories(){
        $categories = \DB::table('categories')
                ->get(['id','category']);
        if(count($categories) == 0){
            return response()->json(['message'=>0]);
        }
        else{
            return response()->json(['message'=>1,'data'=>$categories]);
        }      
    }
//subcategories
    public function subCategories(Request $request){
        
        $categories = \DB::table('sub_categories')
                ->where('category','=',$request->categoryId)
                ->get(['id','sub_category']);
        if(count($categories) == 0){
            return response()->json(['message'=>0]);
        }
        else{
            return response()->json(['message'=>1,'data'=>$categories]);
        }
    }
//feedback
    public function submitFeedback(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'message' => 'required'
        ]);
        if ($validator->fails()) { 
                return response()->json(['message'=>0,'data'=>$validator->errors()]);            
            }
        Feedback::create($request->all());
        return response()->json(['meassage'=>1]);
    }
//products
    public function products(Request $request){
        $subcategory = $request->subcategory;
        $product = \DB::table('products')
                        ->where('sub_category',$subcategory)
                        ->get();
        
        return response()->json(['message'=>1,'data'=>$product]);

    }

    public function productDetails(Request $request){
        $product_id = $request->productId;
        
        $product = \DB::table('products')
                        ->where('id',$product_id)
                        ->get();
        if(count($product) == 0){
            return response()->json(['message'=>0]);
        }
        else{
            return response()->json(['message'=>1,'data'=>$product]);
        }
        
    }

    public function popularProducts(Request $request){
        $product_ids = \DB::table('popular_products')
                        ->pluck('product');
        $products = \DB::table('products')
                        ->whereIn('id',$product_ids)
                        ->limit(20)
                        ->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }
    public function allPopularProducts(Request $request){
        $product_ids = \DB::table('popular_products')
                        ->pluck('product');
        $products = \DB::table('products')
                        ->whereIn('id',$product_ids)
                        ->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }

    public function recommendedProducts(Request $request){
        $product_ids = \DB::table('recommended_products')
                        ->pluck('product');
        $products = \DB::table('products')
                        ->whereIn('id',$product_ids)
                        ->limit(20)
                        ->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }

    public function offerProducts(Request $request){
        $offers = \DB::table('products')
                    ->where('offer_price','>','0')
                    ->get();
        return response()->json(['message'=>1,'data'=>$offers]);
    }

    public function goodQualityProducts(Request $request){
        $product_ids = \DB::table('good_quality_products')
                        ->pluck('product');
        $products = \DB::table('products')
                        ->whereIn('id',$product_ids)
                        ->limit(20)
                        ->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }
    public function allGoodQualityProducts(Request $request){
        $product_ids = \DB::table('good_quality_products')
                        ->pluck('product');
        $products = \DB::table('products')
                        ->whereIn('id',$product_ids)
                        ->get();
        return response()->json(['message'=>1,'data'=>$products]);
    }

//category wise product
public function allCategoryProducts(Request $request){
    $category = $request->categoryId; 
    $products = \DB::table('products')
                    ->where('category',$category)
                    ->get();
    if(count($products)==0){
        return response()->json(['message'=>0]);
    }
    else{
        return response()->json(['message'=>1,'data'=>$products]);
    }
}

//


    public function allSubCategoryProducts(Request $request){
        $sub_category = $request->subCategoryId; 
        $products = \DB::table('products')
                        ->where('sub_category',$sub_category)
                        ->get();
        
        if(count($products)==0){
            return response()->json(['message'=>0]);
        }
        else{
            return response()->json(['message'=>1,'data'=>$products]);
        }
    }
//product image
    public function getProductImage(Request $request)
    {   
        $image = \DB::table('products')->where('id',$request->productId)->pluck('image');
        $headers=array('Content-Type'=>'image/png');
        $image = 'storage/'.$image[0];
        return response()->file($image,$headers);
        
    }
//orders
    public function orderProducts(Request $request){
        $user = $request->user()->id; 

        // $data = \DB::table('orders')
        //                 ->where('customer_id',$user)
        //                 ->join('products','orders.product','=','products.id')
        //                 ->select('orders.quantity','products.*')
        //                 ->get();
        // $data = \DB::table('orders')
        //             ->where('id',$request->orderId)
        //             ->get();
        // $products = array();
        // //return $data;
        // $items =$data[0]->products;
        // return $items[0];
        // foreach($items as $key =>$value){
        //     return $product;
        //     $product_data = \DB::table('products')->where('id',$product->productId)->get(['product','id','price']);
            
        // }
        $data = \DB::table('ordered_products')
                    ->join('products','ordered_products.product_id','=','products.id')
                    
                    ->where('order_id',$request->orderId)
                    ->select('ordered_products.*','products.price','products.product')
                    ->get();
                    
        if(count($data)!==0){
            return response()->json(['message'=>1,'data'=>$data]);
        }
        else{
            return response()->json(['message'=>0,'data'=>$data]);
        }
    }
//place order
    public function placeOrder(Request $request){
        
        $order = new Order;
        
        $products = $request->products;
        foreach($products as $product){
            $item = \App\Product::find($product['productId']);

            if($item->stock < $product['count'])
                return response()->json(['message'=>0,"error"=>"stock not available"]);
        }
       
        $order->user_id = $request->user()->id;
        $order->address = $request->address;
        $order->lat = $request->lat;
        $order->lng = $request->lng;
        $order->payment_mode = $request->type;
        $order->recipient_name = $request->name;
        $order->recipient_phone = $request->phone;
        $order->total_amount = $request->totalAmount;
        $order->delivery_amount = $request->deliveryFee;
        $order->verification_code = rand(1000,9999);
        $order->transaction_id = $request->transaction_id;
        $order->save();
        $order_location = new \App\DeliveryPosition;
        $order_location->order_id = $order->id;
        $order_location->save();
        foreach($products as $product){
            //$products=json_decode($products,true);
            $item = \App\Product::find($product['productId']);

            $ordered_product = new \App\OrderedProduct;
            $ordered_product->product_id = $product['productId'];
            $ordered_product->quantity = $product['count'];
            $ordered_product->order_id = $order->id;
            $ordered_product->save();
            
            $item->decrement('stock',$product['count']);
            $item->save();
        }
//sms        
        $mobile = $request->phone;
        $message = "Dear Customer, Your Order has been received. Your otp is ".$order->verification_code.". Regards, DBJM Sity Mart";
        $response = $this->sendSms($mobile,$message);
        return response()->json(['message'=>1]);
    }

    public function allOrdersUser(Request $request){
        $user = $request->user()->id;
        // $data = \DB::table('orders')
        //         ->leftJoin('users','orders.delivery_boy_id','=','users.id')
        //         ->join('ordered_products','orders.id','=','ordered_products.order_id')
        //         ->join('products','products.id','=','ordered_products.product_id')
        //         ->where('orders.user_id',$user)
        //         ->select('products.product','orders.*','users.name AS deliveryAgentName','users.phone AS deliveryAgentNumber')
        //         ->orderBy('created_at','desc')
        //         ->get();
        $data = \DB::table('orders')
                ->leftJoin('users','orders.delivery_boy_id','=','users.id')
                ->where('orders.user_id',$user)
                ->select('orders.*','users.name AS deliveryAgentName','users.phone AS deliveryAgentNumber','users.vehicle_number')
                ->orderBy('created_at','desc')
                ->get();
        foreach($data as $order){
            $order_id = $order->id;
            $order->products = \DB::table('ordered_products')
                                    ->join('products','products.id','=','ordered_products.product_id')
                                    ->where('order_id','=',$order_id)
                                    ->select('ordered_products.*','products.*')
                                    ->get();
        }
        return response()->json(['message'=>1,'data'=>$data]);
        
    }
//delivery location
    public function deliveryLocation(Request $request){
        $user = $request->user()->id;
        $data = \DB::table('delivery_positions')
                    ->where('order_id',$request->orderId)
                    ->first();
                    
        if($data==null){
            return response()->json(['message'=>0]);
        }else{
            return response()->json(['message'=>1,'lat'=>$data->lat,'lng'=>$data->lng]);
        }
    }
//coupon&voucher
    public function checkCoupon(Request $request){
        $code = $request->code;
        $discount = \DB::table('coupons')
                    ->where([['status','=',1],['coupon_code','=',$code]])
                    ->first();
        //return $request;
        if ($discount===null){
            return response()->json(['message'=>1]);
        }
        else{
            return response()->json(['message'=>0,'discount'=>$discount->discount]);
        }
    }

//search
    public function search(Request $request){
        $category = $request->input('category');
        $min = $request->input('min_price');
        $max = $request->input('max_price');
        if($request->has('category') && $request->has('min_price') && $request->has('max_price')){
            $categoryId= \DB::table('categories')
                        ->where('category',$category)
                        ->value('id');
            $products = \DB::table('products')
                        ->where('category', $categoryId)
                        ->where('price','>', $min)
                        ->where('price','<', $max)
                        ->get();
                    }
        else if($request->has('category') && $request->has('min_price')){
            $categoryId= \DB::table('categories')
            ->where('category',$category)
            ->value('id');

            $products = \DB::table('products')
                        ->where('category',$categoryId)
                        ->where('price','>=',$min)
                        ->get();
                    }
        else if($request->has('category') && $request->has('max_price')){
            $categoryId= \DB::table('categories')
            ->where('category',$category)
            ->value('id');

            $products = \DB::table('products')
                        ->where('category',$categoryId)
                        ->where('price','<=', $max)
                        ->get();
                    }
        else if($request->has('min_price') && $request->has('max_price')){
            $products = \DB::table('products')
                        ->whereBetween('price',[$min,$max])
                        ->get();
                    }
        else if($request->has('min_price')){
                        $products = \DB::table('products')
                        ->where('price','>=',(int)$min)
                        ->get();
                                }
        else if($request->has('max_price')){
            $products = \DB::table('products')
            ->where('price','<=',$max)
            ->get();
                    }
        else if($request->has('category')){
            $categoryId= \DB::table('categories')
            ->where('category',$category)
            ->value('id');

            $products = \DB::table('products')
            ->where('category',$categoryId)
            ->get();
                    }
        if(count($products) == 0){
            return response()->json(['message'=>$category]);
        }
        else{
            return response()->json(['message'=>1,'data'=>$products]);
        }
    }

    //all coupons
    public function allActiveCoupons()
    {   
        $data = \DB::table('coupons')->where('active','=',1)->get();
        return response()->json(['message'=>1,'data'=>$data]);    
    }

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
    public function getCategoryImage(Request $request)
    {
        $image = \DB::table('categories')->where('id',$request->categoryId)->pluck('image');
        $headers=array('Content-Type'=>'image/png');
        
        $image = 'storage/'.$image[0];
        return response()->file($image,$headers);
    }

}