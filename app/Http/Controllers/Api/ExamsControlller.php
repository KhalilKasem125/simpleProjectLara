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

class ExamsControlller extends Controller
{

    public function setExam(Request $request , $id ){


        //Validations
        $request->validate([
            'qestions_number'=>'required|numeric',
            'success_degree'=>'required|numeric',
            'Exam_Name'=>'required',
            'Exam_Type'=>'required',
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
            'Exam_Type'=>$request->Exam_Type,

        ]);

        //sending response
        return response()->json([
            'status'=>true,
            'message'=>'تم اضافة الامتحان بنجاح '
        ]);

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

        // return response()->json([
        //     'status' => true,
        //     'exam' => $exam
        // ]);
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

