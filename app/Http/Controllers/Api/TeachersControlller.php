<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Mockery\Matcher\Subset;

class TeachersControlller extends Controller
{
    //Adding Teacher Informations
    //Post - that if I want to add it without enter subject
    // public function addTeacher(Request $request)
    // {

    //     //Validations
    //     $request->validate([
    //         "first_name"=>"required|min:3",
    //         "last_name"=>"required|min:3",
    //         "description"=>"required",
    //         "phone_no"=>"required|max:9",
    //         // "date_of_birth"=>"required",
    //         "teaching_duration"=>"required",
    //         "subject_name"=>"required"
    //     ]);

    //     $subject = Subject::where('subject_name',$request->subject_name)->first();
    //     $subjectId = $subject->id;
    //     //Saving Teacher Object
    //     $teacher = Teacher::create([
    //         "first_name"=>$request->first_name,
    //         "last_name"=>$request->last_name,
    //         "description"=>$request->description,
    //         "phone_no"=>$request->phone_no,
    //         "subject_name"=>$request->subject_name,
    //         "date_of_birth"=>$request->date_of_birth,
    //         "teaching_duration"=>$request->teaching_duration,
    //         "subject_id"=>$subjectId
    //     ]);

    //     //sending response
    //     return response()->json([
    //         "status"=>true,
    //         "message"=>"The teacher has been added successfully"
    //     ]);
    // }

    public function addTeacherfromSubject(Request $request , $id)
    {

        //Validations
        $request->validate([
            "first_name"=>"required|min:3",
            "last_name"=>"required|min:3",
            "description"=>"required",
            "phone_no"=>"required|max:9",
            // "date_of_birth"=>"required",
            "teaching_duration"=>"required",
           // "subject_name"=>"required"
        ]);
        $existingTeacher = Teacher::where('subject_id', $id)
                ->where('first_name', $request->first_name)
                ->where('last_name', $request->last_name)
                ->first();

        if ($existingTeacher) {
            return response()->json([
                "status" => false,
                "message" => "المعلم موجود بالفعل في هذه المادة",
            ], 422); // HTTP status code 422 Unprocessable Entity
        }

        //Saving Teacher Object
        $teacher = Teacher::create([
            "first_name"=>$request->first_name,
            "last_name"=>$request->last_name,
            "description"=>$request->description,
            "phone_no"=>$request->phone_no,
            "subject_name"=>Subject::find($id)->subject_name,
            "date_of_birth"=>$request->date_of_birth,
            "teaching_duration"=>$request->teaching_duration,
            "subject_id"=>$id
        ]);

        //sending response
        return response()->json([
            "status"=>true,
            "message"=>" تم اضافه معلومات المعلم "
        ]);
    }

    //Only Admins Can access on full Teachers Informations
    //Get
    public function showTeachersDetailsForAdmins($id){
        $teachers_info = Subject::find($id)->teachers;

        return response()->json([
            "status"=>true ,
            "message"=>"معلومات المعلمين  ",
            "data"=>$teachers_info
        ]);
    }

    //Students can get somInfo
    //Get
    public function showTeachersDetailsForStudents($id)
    {

        $teachers = Subject::find($id)->teachers;

        $teachersData = [];
        foreach ($teachers as $teacher) {
            $teachersData[] = [
                'first_name' => $teacher->first_name,
                'last_name'=>$teacher->last_name,
                'phone_no' => $teacher->phone_no,
                'teaching_duration' => $teacher->teaching_duration,
                'subject_name' => $teacher->subject_name,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'معلومات المعلمين ',
            'data' => $teachersData,
        ]);
    }

    //Only Admins can reach
    //Delete
    public function deleteTeacher($id)
    {
        //fetch the object with the id
        $teacher_deleted =Teacher::find($id);

        if($teacher_deleted){
            $teacher_deleted->delete();

            return response()->json([
                "status"=>true,
                "message"=>"تم حذف المعلم بنجاح "
            ]);
        }else{
            return response()->json([
                "status"=>false,
                "message"=>"المعلم غير موجود"
            ],404);
        }
    }

    
    public function showingAllTeachers(){

        $teachers = Teacher::get();
        if($teachers){
            return response()->json([
                'status'=>true,
                'message'=>'معلومات المعلمين',
                'data'=>$teachers
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'لا يوجد معلمين '

            ]);
        }

    }

}
