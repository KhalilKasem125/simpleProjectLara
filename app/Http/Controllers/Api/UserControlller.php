<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserControlller extends Controller
{

    //User Registration -Post
    public function register(Request $request){

        //validation
        $request->validate([
            "first_name"=>"required",
            "last_name"=>"required",
            "password"=>"required|confirmed:min:8|max:20",
            "email"=>"required|email|unique:users",
            "phone_no"=>"required"
        ]);

        //Informations Saving
        $user = new User();
        $user->first_name = $request->first_name ;
        $user->last_name = $request->last_name ;
        $user->email = $request->email ;
        $user->phone_no = $request->phone_no ;
        $user->password = bcrypt($request->password) ;
        $user->role = "user" ;
        $user->save();

        //sending response
        return response()->json([
            "status"=>1,
            "message"=>"the user has been registered successfully"
        ]);
    }

    //User Login -Post
    public function login(Request $request){

        //validation
        $request->validate([
            "email"=>"required|email",
            "password"=>"required"
        ]);

        //check if User Or Not(Credintials)
        if(!$token =auth()->attempt(["email"=>$request->email,"password"=>$request->password])){
            return response()->json([
                "status"=>0,
                "message"=>"Invalid Credintials"
            ],400);
        }else{
            return response()->json([
                "status"=>1,
                "message"=>"Your Logged in Successfully",
                "access_token"=>$token
            ]);
        }
    }

    //User Profile -GET
    public function profile(){

        $user_data = auth()->user();

        return response()->json([
            "status"=>1,
            "message"=>"User Profile",
            "data"=>$user_data
        ]);
    }

    //Refresh Token -GET
    public function refreshToken(){
        $new_token = auth()->refresh();

        return response()->json([
            "status"=>true,
            "message"=>"New Access Toke Generated Successfully",
            "New_Token"=>$new_token
        ]);

    }

    //User Logout -GET
    public function logout(){
        auth()->logout();

        return response()->json([
            "status"=>1,
            "message"=>"User Logged Out Successfully"
        ]);
    }
}
