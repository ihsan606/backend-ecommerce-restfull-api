<?php

namespace App\Http\Controllers\Api\Customer;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request){
        //set validation rules
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        //get email and password
        $credentials = $request->only('email','password');

        if(!$token = auth()->guard('api_customer')->attempt($credentials)){
            return response()->json([
                'success' =>false,
                'message' => 'Email and Password Invalid'
            ],401);
        }

        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user(),
            'token' => $token
        ]);
    }

    public function getUser() {
        //response data "user" yang sedang login
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api_customer')->user()
        ], 200);
    }

    public function refreshToken(Request $request) {
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
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' =>true,
        ], 200);
    }
}
