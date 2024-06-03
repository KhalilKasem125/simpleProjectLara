<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubjectsControlller extends Controller
{

    //this subject only the admin can Add
    public function addSubject(Request $request){

        //validation
        $request->validate([
            "subject_name"=>"required"
        ]);
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                "status" => false,
                "message" => "ليس لديك الصلاحية للدخول"
            ], 401);
        }
        //Subject Creation
        $subject = Subject::create([
            "subject_name"=>$request->subject_name,
            'created_by'=>$user->id
        ]);
        //sending response
        return response()->json([

            "status"=>true,
            "messge"=>"تم انشاء المادة بنجاح "
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
                "message"=>"تم حذف المادة بنجاح "
            ]);
        }else{
            return response()->json([
                "status"=>false ,
                "message"=>"المادة غير موجودة"
            ],400);
        }
    }
    //this is for the admins only can use this property
    public function getSubjects(){

        $subjects = Subject::get();

        if($subjects){
            return response()->json([
                "status"=>true,
                "message"=>"كل المواد",
                "Subjects"=>$subjects
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>"لا يوجد مواد بعد "
            ]);
        }
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
                "message"=>"المادة غير موجودة ",
            ]);
        }
    }

    public function getOptions($id)
    {

        $subject = Subject::with(['videos', 'photos', 'files', 'books', 'teachers', 'exams'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'محتويات الماده',
            'data' => [
                'vidos'=>'videos',
                'videos_num' => $subject->videos->count(),
                'photos'=>'photos',
                'photos_num' => $subject->photos->count(),
                'photos'=>'photos',
                'files_num' => $subject->files->count(),
                'files'=>'files',
                'books_num' => $subject->books->count(),
                'books'=>'books',
                'teachers_num' => $subject->teachers->count(),
                'exams'=>'exams',
                'exams_num' => $subject->exams->count(),
            ],
        ]);
    }
}
