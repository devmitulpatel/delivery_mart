<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Restaurant;
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
//registering restaurant user and a restaurant
    public function register(Request $request) 
        { 
            $validator = Validator::make($request->all(), [ 
                'name' => 'required',  
                'password' => 'required', 
                'phone' => 'required|unique:users|digits:10',
                'role_id' => 4,
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


  
}
