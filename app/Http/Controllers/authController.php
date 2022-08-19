<?php

namespace App\Http\Controllers;

use App\Models\personalTokenModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class authController extends Controller
{


    public function index(Request $request)
    {
        return $this->checkValidity($request->bearerToken());
    }


    public function login_post(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

       
        $user = User::where('email', $request->email)->first();

        if($user){
            // check password
            if($user->password == $request->password){

                // check token

                $user->tokens()->delete();
                $token = $user->createToken('token')->plainTextToken;

                return response()->json([
                    'message' => "login Succes",
                    'user' => $user,
                    'token' => $token
                ] ,200);


            }else{
                return response()->json("password does not match");
            }

        }else{
            return response()->json("User not Found");
        }


    }


    public function register_post(Request $request)
    {
        // name 
        // email 
        // password 

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $user = User::create($request->only([
            'name','email','password'
        ]));


        // crate token

        $token = $user->createToken('token')->plainTextToken;
        return compact('user', 'token');

    }



    public function checkValidity($token_Request)
    {
   
       // get the token user
        $token = PersonalAccessToken::findToken($token_Request);

        if($token){

        $userInfo = $token->tokenable;

       // check and the validity 
        $tokenField = personalTokenModel::where('tokenable_id', $userInfo->id)->first();
        $withAddedminiute = Carbon::parse($tokenField->created_at)->addMinutes(5);

        if($withAddedminiute < now()){

            $tokenField->delete();
            $Newtoken = $userInfo->createToken('token')->plainTextToken;

            return response()->json([
                "message" => 'Previous token has expired here is your new token',
                'Newtoken' => $Newtoken
            ],401);


        }else{

            return response()->json([
                "message" => 'Welcome Auth User',
                'user'=> $userInfo,
            ],200);

            
        }

    }else{
        return response()->json([
            "message" => 'unautherized',
        ],401);
        
    }



    }
}
