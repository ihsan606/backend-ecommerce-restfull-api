<?php

namespace App\Http\Controllers\api\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function index(Request $request){
        //set validasi
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        //respon error validasi
        if($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        //get email dan password dari input
        $credential = $request->only('email', 'password');

        //check email dan password apakah sudah sesuai. jika tidak maka muncul pesan error
        if(!$token = auth()->guard('api_admin')->attempt($credential)){
            //respon login failed
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect' 
            ],401);
        }
        
        //respon login success dan generate token 
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_admin')->user(),
            'token' => $token
        ],200);


    }


    public function getUser()
    {
        //response data "user" yang sedang login
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api_admin')->user()
        ], 200);
    }


    public function refreshToken(Request $request){

        //refresh "token"
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        //set user dengan "token" baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set header "Authorization" dengan type Bearer + "token" baru
        $request->headers->set('Authorization','Bearer '.$refreshToken);

        //response data "user" dengan "token" baru
        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $refreshToken,  
        ], 200);
    }

    public function logout() {

        //remove toke jwt
        $removeToken =JWTAuth::invalidate(JWTAuth::getToken());

        //response success logout
        return response()->json([
            'success' => true
        ], 200);
    }
}
