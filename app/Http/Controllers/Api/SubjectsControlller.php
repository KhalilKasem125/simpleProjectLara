<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsControlller extends Controller
{

    //this subject only the admin can Add
    public function addSubject(Request $request){
        //validation
        $request->validate([
            "subject_name"=>"required"
        ]);
        //Subject Creation
        $subject = Subject::create([
            "subject_name"=>$request->subject_name
        ]);
        //sending response
        return response()->json([
            "status"=>true,
            "messge"=>"subject has been created successfully"
        ]);
    }
    //this subject only the admin can delete
    public function deleteSubject($id)
    {
        //object deleted
        $sub_deleted = Subject::find($id);

        if($sub_deleted){
            $sub_deleted->delete();
            return response()->json([
                "status"=>true ,
                "message"=>"The Subject has been deleted Successfully"
            ]);
        }else{
            return response()->json([
                "status"=>false ,
                "message"=>"The Subject is not found "
            ],400);
        }
    }
    //this is for the admins only can use this property
    public function getSubjects(){
        $subjects = Subject::get();

        return response()->json([
            "status"=>true,
            "message"=>"the all subjects",
            "Subjects"=>$subjects
        ]);
    }
    //this is for the admins only can use this property
    public function getSingleSubject($id){
        $subject_wanted = Subject::find($id);

        if($subject_wanted){
            return response()->json([
                "status"=>true,
                "message"=>"the subject is",
                "Subjects"=>$subject_wanted
            ]);
        }else{
            return response()->json([
                "status"=>false,
                "message"=>"the subjects is not found ",
            ]);
        }
    }

}
