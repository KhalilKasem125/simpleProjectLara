<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AdminsControlller extends Controller
{

    //Post - Admins only registration
    public function AdminRegister(Request $request){

        //validation
        $request->validate([
            "first_name"=>"required|max:50|min:2",
            "last_name"=>"required:max:50|min:2",
            "password"=>"required|confirmed|max:20|min:8",
            "email"=>"required|email|unique:admins",
            "phone_no"=>"required|max:9",
        ]);

        //Admin Creation
        $admin =Admin::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'password'=>bcrypt($request->password),
            'email'=>$request->email,
            'role' => "admin",
            "phone_no"=>$request->phone_no
        ]);

        return response()->json([
            "status"=>true,
            "message"=>"تم تسجيل الادمن بنجاح "
        ]);

    }

    public function SuperAdminRegister(Request $request){


        $existingSuperAdmin = Admin::where('role', 'super_admin')->first();

        if ($existingSuperAdmin) {
            return response()->json([
                "status" => false,
                "message" => " عذرا , يوجد مشرف لهذا الموقع مسبقا"
            ],404);
        }

        //validation
         $request->validate([
            "first_name"=>"required|max:50|min:2",
            "last_name"=>"required:max:50|min:2",
            "password"=>"required|confirmed|max:20|min:8",
            "email"=>"required|email|unique:admins",
            "phone_no"=>"required|max:9",
        ]);


        //Admin Creation
        $admin =Admin::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'password'=>bcrypt($request->password),
            'email'=>$request->email,
            'role' => "super_admin",
            "phone_no"=>$request->phone_no
        ]);

        return response()->json([
            "status"=>true,
            "message"=>"تم تسجيل المشرف بنجاح "
        ]);

    }

    //Post - Admin and Super Admin Registration
    public function AdminLogin(Request $request)
    {

        $request->validate([
            "email"=>"required|email",
            "password"=>"required",
        ]);

        if(!$token =Auth::guard('admin-api')->attempt(["email"=>$request->email,"password"=>$request->password])){
            return response()->json([
                "status"=>0,
                "message"=>"معلومات تسجيل خاطئة "
            ],404);
        }else{
            return response()->json([
                "status"=>1,
                "message"=>"قمت بالتسجيل بنجاح",
                "access_token"=>$token
            ]);
        }
    }

    //Only the super admin can use this function to delete an admin
    //Get
    public function AdminsInformationsShowing()
    {

        $super_admin_id =Admin::where('role',"super_admin")->first()->id;

        //$admin = new Admin();
        $admins =Admin::where("id","!=",$super_admin_id)->get();

        if($admins){
            return response()->json([
                "status"=>true,
                "message"=>"معلومات الادمنز ",
                "data"=>$admins
            ]);
        }else{
            return response()->json([
                "status"=>false,
                "message"=>" لا يوجد مشرفين بعد"
            ],404);
        }
    }
    
    //That if I want to refresh token
    //Get-
    public function refreshToken(){
        $new_token = auth()->refresh();

        return response()->json([
            "status"=>true,
            "message"=>"New Access Toke Generated Successfully",
            "New_Token"=>$new_token
        ]);

    }

    //If I want to showing admin's profile Informations
    //Get-
    public function profile()
    {
        // Retrieve the authenticated admin
        $admin_data = Auth::guard('admin-api')->user();

        // Return the admin profile data
        return response()->json([
            "status" => 1,
            "message" => "بروفايل الادمن",
            "data" => $admin_data
        ]);

    }

    //Only the super admin can use this function to delete an admin
    //Delete - when super_admin want to delete admin should send admin's name and its id
    public function AdminDelete( $id ){

        $admin_deleted =Admin::find($id);

        if($admin_deleted){
            $admin_deleted->delete();
            return response()->json([
                "status"=>true,
                "message"=>"تم حذف الادمن بنجاح "
            ]);
        }else{
            return response()->json([
                "status"=>false,
                "message"=>" الادمن غير موجود"
            ],404);
        }
    }

    public function logout(){
        auth()->logout();

        return response()->json([
            "status"=>1,
            "message"=>"تم تسجيل الخروج بنجاح "
        ]);
    }

}
