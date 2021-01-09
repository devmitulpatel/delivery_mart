<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;
//login
    public function login(Request $request){ 
        if(Auth::attempt(['phone' => request('phone'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['token' => $success['token'],'data'=>$user->id, 'name'=>$user->name,'message'=>1,'address'=>$user->address]); 
        } 
        else{ 
            return response()->json(['message'=>0]); 
        } 
    }
//register
    public function register(Request $request) 
        { 
            $validator = Validator::make($request->all(), [ 
                'name' => 'required',  
                'password' => 'required', 
                'phone' => 'required|unique:users|digits:10',
                'role_id' => 'required|between:0,4',
                'address' => 'required'
            ]);
        if ($validator->fails()) { 
                    return response()->json(['message'=>0,'data'=>$validator->errors()]);            
                }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')->accessToken; 
        return response()->json(['message'=>1,'data'=>$user->id,'token'=>$success['token'],'address'=>$user->address]); 
        
        }
//logout
        public function logout(Request $request){
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 1
            ]); 
        }
//details
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
//updateProfile
    public function updateProfile(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();
        return response()->json(['message'=>1]);
    }
    public function uploadProfileImage(Request $request)
    {
        //$user = User::findOrFail($request->user()->id);
        $image = $request->image;
        $ext = explode(';base64',$image);
        $ext = explode('/',$ext[0]);
        $ext = $ext[1];
        $replace = substr($image,0,strpos($image,',')+1);
        $image = str_replace($replace,'',$image);
        $image = str_replace('','+',$image);
        $imageName = \Str::random(10).'.'.$ext;
        \Storage::disk('public')->put('users/'.$imageName,base64_decode($image));
        $user = $request->user();
        $user->avatar = 'users/'.$imageName;
        $user->save();
        return response()->json(['message'=>1]);

    }
    public function getUserImage(Request $request)
    {
        $user = $request->userId;
        $image = \DB::table('users')->where('id',$request->userId)->pluck('avatar');
        $headers=array('Content-Type'=>'image/png');
        
        $image = 'storage/'.$image[0];
        return response()->file($image,$headers);
    }
}
