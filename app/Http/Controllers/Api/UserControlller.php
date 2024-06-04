<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserControlller extends Controller
{


    //User Registration -Post
    public function register(Request $request){

        //password
        //password_confirmation
        //
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
            "message"=>"تم تسجيلك بنجاح "
        ]);
    }

    //this func enable user to update all his informations
    //Put
    public function updateUserInformations(Request $request , $id){


        $request->validate([
            'password'=>"required|confirmed"
        ]);

        if(User::where("id",$id)->exists()){
            $user = User::find($id);
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    "status" => false,
                    "message" => "لا يوجد تطابق"
                ], 401);
            }
            $user->first_name = !empty($request->first_name) ? $request->first_name : $user->first_name ;
                $user->last_name = !empty($request->last_name) ? $request->last_name : $user->last_name ;
                $user->email = !empty($request->email) ? $request->email : $user->email ;
                $user->phone_no = !empty($request->phone_no) ? $request->phone_no : $user->phone_no ;
                $user->password = !empty(bcrypt($request->new_password)) ? $request->new_password : $user->password ;
                // $user->updated_by = auth()->user()->id ;
            $user->save();

            return response()->json([
                'status'=>true,
                'message'=>'تم تعديل المعلومات بنجاح'
            ]);
        }else{
            return response()->json([
                "status" => false,
                "message" => "هذا المستخدم غير موجود"
            ], 401);

        }

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
                "message"=>"معلومات تسجيل خاطئة "
            ],400);
        }else{
            return response()->json([
                "status"=>1,
                "message"=>"تم تسجيل الدخول بنجاح ",
                "access_token"=>$token
            ]);
        }
    }

    //User Profile -GET
    public function profile(){

        $user_data = auth()->user();

        return response()->json([
            "status"=>1,
            "message"=>"بروفايل المستخدم ",
            "data"=>$user_data
        ]);
    }

    //Refresh Token -GET
    public function refreshToken(){

        $new_token = auth()->refresh();

        return response()->json([
            "status"=>true,
            "message"=>"تم توليد توكين جديد بنجاح",
            "New_Token"=>$new_token
        ]);

    }

    //User Logout -GET
    public function logout(){

        auth()->logout();

        return response()->json([
            "status"=>1,
            "message"=>"تسجيل الخروج تم بنجاح "
        ]);
    }

}
