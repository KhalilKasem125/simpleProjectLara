<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionsControlller extends Controller
{
    public function setQuestio(Request $request , $id ){
        //Validations
        //also we have difficulty and its an enumeration between  three choices (hard ,easy ,medium)
        //but this can be nullable so we dont want to validate to be optionally added
        $request->validate([
            'question_text'=>'required',
        ]);

        //object creating
        $question = Question::create([
            'question_text'=>$request->question_text,
            'exam_id'=>$id,
            'difficulty'=>$request->difficulty
        ]);

        //sending response
        if($question){
            return response()->json([
                'status'=>true ,
                'message'=>"the Question has been saved successfully "
            ]);
        }else{
            return response()->json([
                'status'=>false ,
                'message'=>"Invalid Informations "
            ]);
        }
    }
    public function setQuesti(Request $request , $id ){
        //Validations
        $request->validate([
            'question_text'=>'required|unique:questions',
        ]);

        // Get the Exam record
        $exam = Exam::find($id);

        // Check if the number of existing questions is less than the allowed number
        if ($exam->questions()->count() < $exam->questions_number) {
            //object creating
            $question = Question::create([
                'question_text'=>$request->question_text,
                'exam_id'=>$id,
                'difficulty'=>$request->difficulty
            ]);

            //sending response
            if($question){
                return response()->json([
                    'status'=>true ,
                    'message'=>"the Question has been saved successfully "
                ]);
            }else{
                return response()->json([
                    'status'=>false ,
                    'message'=>"Invalid Informations "
                ]);
            }
        } else {
            // Return an error response if the limit is reached
            return response()->json([
                'status'=>false ,
                'message'=>"You have reached the maximum number of questions for this exam."
            ], 422); // Use 422 Unprocessable Entity status code
        }
    }
    public function setQuestion(Request $request , $id ){
        //Validations
        $request->validate([
            // 'question_text'=>'required|unique:questions',
            'question_text' => 'required|unique:questions,question_text,NULL,id,exam_id,' . $id,
        ]);

        // Get the Exam record
        $exam = Exam::find($id);

        // Check if the number of existing questions for this exam is less than the allowed number
        if ($exam->questions()->where('exam_id', $id)->count() < $exam->qestions_number) {
            //object creating
            $question = Question::create([
                'question_text'=>$request->question_text,
                'exam_id'=>$id,
                'difficulty'=>$request->difficulty
            ]);

            //sending response
            if($question){
                return response()->json([
                    'status'=>true ,
                    'message'=>"the Question has been saved successfully "
                ]);
            }else{
                return response()->json([
                    'status'=>false ,
                    'message'=>"Invalid Informations "
                ]);
            }
        } else {
            // Return an error response if the limit is reached
            return response()->json([
                'status'=>false ,
                'message'=>"You have reached the maximum number of questions for this exam."
            ], 422); // Use 422 Unprocessable Entity status code
        }
    }


    public function getQuestions($id){
        $questions = Exam::find($id)->questions ;

        if($questions){
            return response()->json([
                'status'=>true ,
                'Questions'=>$questions,

            ]);
        }else{
            return response()->json([
                'status'=>false ,
                'message'=>"There are no questions"
            ]);
        }
    }

}
