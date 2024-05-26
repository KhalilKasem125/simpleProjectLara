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
            "message"=>"Teachers Informations ",
            "data"=>$teachers_info
        ]);
    }

    //Students can get somInfo
    //Get
    public function showTeachersDetailsForStudents($id)
    {

        // $teachers_info = Teacher::select('first_name', 'last_name',
        // 'phone_no','teaching_duration' ,'subject_name')
        //             ->get();
        // $teacherInfo = Teacher::select('first_name', 'last_name', 'phone_no', 'teaching_duration', 'subject_name')
        // ->where('id', $id) // Add the where clause to filter by ID
        // ->get();

        // return response()->json([
        //     "status"=>1 ,
        //     "message"=>"Teachers Informations",
        //     "data"=>$teacherInfo
        // ]);
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
            'message' => 'Photos retrieved successfully',
            'data' => $teachersData,
        ]);
    }

    //Only Admins can reach
    //Delete
    public function deleteTeacher($id)
    {

        $teacher_deleted =Teacher::find($id);


        $teacher_deleted->delete();
        return response()->json([
            "status"=>true,
            "message"=>"Teacher has been deleted Successfully "
        ]);

    }

}
