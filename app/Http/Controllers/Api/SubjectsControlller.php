<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Subject;
// use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
class SubjectsControlller extends Controller
{

    //this subject only the admin can Add
    public function addSubject(Request $request){

        //validation
        $request->validate([
            "subject_name"=>"required:unique:subjects"
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
        $admin_id = auth()->user()->id ;
        $admin = Admin::find($admin_id);
        // $find = Admin::where('role','super_admin');
        // ->Subject::where('created_by',$admin_id)->first();
        $sup = Subject::find($id);

        if($admin->role == 'super_admin' ||$sup->created_by == $admin_id ){
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
        }else{
            return response()->json([
                'status' => true,
                'message' => 'ليس لديك الصلاحية للقيام بذلك '
            ],402);
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

    // public function updateSubject(Request $request , $id ){

    //     // $request->validate([
    //     //     "subject_name"=>"required:unique:subjects"
    //     // ]);

    //     $subject = Subject::find($id);
    //     if($subject){
    //         $subject->subject_name = !empty($request->subject_name) ? $request->subject_name : $subject->subject_name ;
    //         $subject->updated_by = auth()->user()->id ;
    //         $subject ->save();

    //         return response()->json([
    //             'status'=>true,
    //             'message'=>'تم التعديل على معلومات المادة بنجاح'
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status'=>false,
    //             'message'=>'المادة غير موجودة'
    //         ],401);
    //     }

    //     //هام
    //     // $subject = Subject::find($id);
    //     // if($subject){
    //     //     $subject->subject_name = !empty($request->new_subject_name&&$request->validate([
    //     //         "new_subject_name"=>"required:unique:subjects"
    //     //     ])) ? $request->new_subject_name : $subject->subject_name ;
    //     //     $subject->updated_by = auth()->user()->id ;
    //     //     $subject ->save();

    //     //     return response()->json([
    //     //         'status'=>true,
    //     //         'message'=>'تم التعديل على معلومات المادة بنجاح'
    //     //     ]);
    //     // }else{
    //     //     return response()->json([
    //     //         'status'=>false,
    //     //         'message'=>'المادة غير موجودة'
    //     //     ],401);
    //     // }

    // }
    public function updateSubject(Request $request, $id) {
        $subject = Subject::find($id);

        if ($subject) {
            // 1. Check for uniqueness even if new_subject_name is empty
            $validator = Validator::make($request->all(), [
                'new_subject_name' => 'unique:subjects,subject_name,' . $id, // Exclude the current subject ID
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'الماده موجودة مسبقا',
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }

            // 2. Update subject name if it's provided
            if (!empty($request->new_subject_name)) {
                $subject->subject_name = $request->new_subject_name;
            }

            // 3. Update updated_by
            $subject->updated_by = auth()->user()->id;

            $subject->save();

            return response()->json([
                'status' => true,
                'message' => 'تم التعديل على معلومات المادة بنجاح'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'المادة غير موجودة'
            ], 401);
        }
    }
}
