<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Exams_result;
use App\Models\Option;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class ExamsControlller extends Controller
{
    //post - adding an exam
    public function setExam(Request $request , $id ){

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                "status" => false,
                "message" => "ليس لديك الصلاحية للدخول"
            ], 401);
        }
        //Validations
        $request->validate([
            'exam_time'=>"required|numeric",
            'qestions_number'=>'required|numeric',
            'success_degree'=>'required|numeric',
            'Exam_Name'=>'required',
            'exam_day_start_point' => 'required|date',
            'exam_day_end_point' => 'required|date|after:exam_day_start_point',

        ]);

        //object saving
        $exam = Exam::create([

            'subject_id'=>$id ,
            'qestions_number'=>$request->qestions_number,
            'exam_day_start_point' => Carbon::parse($request->input('exam_day_start_point')),
            'exam_day_end_point' => Carbon::parse($request->input('exam_day_end_point')),
            'success_degree'=>$request->success_degree,
            'Exam_Name'=>$request->Exam_Name,
            'exam_time'=>$request->exam_time,
            'created_by' => $user->id

        ]);

        //sending response
        return response()->json([
            'status'=>true,
            'message'=>'تم اضافة الامتحان بنجاح '
        ]);

    }

    //update Exam Informations and with adding the admin how did
    //put
    // public function updateExam(Request $request , $id ){

    //     $exam = Exam::find($id);

    //     if($exam){

    //         $exam->qestions_number = !empty($request->qestions_number) ? $request->qestions_number : $exam->qestions_number ;
    //         $exam->exam_day_start_point = !empty($request->exam_day_start_point) ? $request->exam_day_start_point : $exam->exam_day_start_point ;
    //         $exam->exam_day_end_point = !empty($request->exam_day_end_point) ? $request->exam_day_end_point : $exam->exam_day_end_point ;
    //         $exam->Exam_Name = !empty($request->Exam_Name) ? $request->Exam_Name : $exam->Exam_Name ;
    //         $exam->exam_time = !empty($request->exam_time) ? $request->exam_time : $exam->exam_time ;
    //         $exam->success_degree = !empty($request->success_degree) ? $request->success_degree : $exam->success_degree ;
    //         $exam->updated_by = auth()->user()->id;
    //         $exam->save();

    //         return response()->json([
    //             'status'=>true,
    //             'message'=>'تم التعديل على معلومات الامتحان بنجاح'
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status'=>false ,
    //             'message'=>'الامتحان غير موجود'
    //         ],401);
    //     }



    // }
    public function updateExam(Request $request, $id) {
        $exam = Exam::find($id);

        if ($exam) {
            // 1. Get the current subject_id
            $subjectId = $exam->subject_id;

            // 2. Validate for uniqueness within the same subject_id
            $validator = Validator::make($request->all(), [
                'Exam_Name' => 'unique:exams,Exam_Name,' . $id . ',id,subject_id,' . $subjectId, // Exclude the current exam and specify subject_id
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exam name already exists for this subject',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 3. Update exam details
            $exam->qestions_number = !empty($request->qestions_number) ? $request->qestions_number : $exam->qestions_number;
            $exam->exam_day_start_point = !empty($request->exam_day_start_point) ? $request->exam_day_start_point : $exam->exam_day_start_point;
            $exam->exam_day_end_point = !empty($request->exam_day_end_point) ? $request->exam_day_end_point : $exam->exam_day_end_point;
            $exam->Exam_Name = !empty($request->Exam_Name) ? $request->Exam_Name : $exam->Exam_Name;
            $exam->exam_time = !empty($request->exam_time) ? $request->exam_time : $exam->exam_time;
            $exam->success_degree = !empty($request->success_degree) ? $request->success_degree : $exam->success_degree;
            $exam->updated_by = auth()->user()->id;

            $exam->save();

            return response()->json([
                'status' => true,
                'message' => 'تم التعديل على معلومات الامتحان بنجاح'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'الامتحان غير موجود'
            ], 401);
        }
    }


    public function getExams($id){


        $exam = Subject::find($id)->exams ;

        //cheching if the subject has exams or not
        if($exam){
            return response()->json([
                'status'=>true,
                'message'=>"Exams ",
                'data'=>$exam
            ]);
        }else{
            return response()->json([
                'status'=>true,
                'message'=>"المادة ليس لها امتحانات بعد  ",
            ]);
        }
    }

    public function getExamTemplate($id)
    {


        $exam = Exam::with('questions.options')->find($id);

        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'الامتحان ليس موجودا'
            ], 404); // Use a 404 Not Found status code if the exam doesn't exist
        }

        return new ExamResource($exam);
    }

    public function getExamTemplateForWeb($id)
    {


        $exam = Exam::with('questions.options')->find($id);

        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'الامتحان ليس موجودا'
            ], 404); // Use a 404 Not Found status code if the exam doesn't exist
        }

        return response()->json([
            'status' => true,
            'exam' => $exam
        ]);
    }

    public function submitExam(Request $request, $examId)
    {



        $user_id = auth()->user()->id;

        // 1. Validate the input:
        $request->validate([
            'answers' => 'required|array', // Array of answers
            'answers.*.question_id' => 'required|exists:questions,id', // Each answer has question_id
            'answers.*.selected_option_id' => 'required|exists:options,id', // Each answer has selected_option_id
        ]);

        // 2. Fetch the exam:
        $exam = Exam::with('questions.options')->find($examId);
        if (!$exam) {
            return response()->json(['status' => false, 'message' => 'الامتحان غير موجود '], 404);
        }

        // 3. Process answers and calculate score:
        $score = 0;
        $totalQuestions = $exam->qestions_number;

        foreach ($request->input('answers') as $answer) {
            $selectedOptionId = $answer['selected_option_id'];

            // Direct database query
            $selectedOption = Option::find($selectedOptionId);

            // Detailed output
            if ($selectedOption && $selectedOption->is_correct) {
                $score++;
            }

            // ... your score calculation logic
        }

        // 4. Calculate the degree:
        $degree = ($score / $totalQuestions) * 100;

        // 5. Determine if the student passed (using the exam's success_degree)
        $passed = $degree >= $exam->success_degree;

        // 6.  Save the exam result (optional, you might need a separate table for this):
        // ...  (code to save exam results to the database, if needed)

        if($passed){
            $status = "succeeded";
        }else{
            $status = "faild" ;
        }
        //creating an exam_result object
        $ex_res = Exams_result::create([
            'user_id'=>$user_id ,
            'exam_id'=>$examId,
            'score'=>$degree,
            'finished_at'=>Carbon::now('Asia/Damascus'),
            'status'=>$status
        ]);


        // 7. Return the results:
        return response()->json([
            'status' => true,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'degree' => $degree,
            'passed' => $passed,
        ]);
    }

    public function deleteExam( $examId)
    {
        // Find the exam
        $exam = Exam::where('id', $examId)
                    ->first();

        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'الامتحان غير موجود.'
            ], 404);
        }

        // Delete the exam
        $exam->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الامتحان بنجاح .'
        ]);
    }

}

