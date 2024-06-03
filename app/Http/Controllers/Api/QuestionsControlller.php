<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionsControlller extends Controller
{
    // public function setQuestio(Request $request , $id ){

    //     //Validations
    //     //also we have difficulty and its an enumeration between  three choices (hard ,easy ,medium)
    //     //but this can be nullable so we dont want to validate to be optionally added
    //     $request->validate([
    //         'question_text'=>'required',
    //     ]);

    //     //object creating
    //     $question = Question::create([
    //         'question_text'=>$request->question_text,
    //         'exam_id'=>$id,
    //         'difficulty'=>$request->difficulty
    //     ]);

    //     //sending response
    //     if($question){
    //         return response()->json([
    //             'status'=>true ,
    //             'message'=>"تم حفظ السؤال  "
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status'=>false ,
    //             'message'=>"معلومات خاطئه  "
    //         ]);
    //     }
    // }

    // public function setQuesti(Request $request , $id ){
    //     //Validations
    //     $request->validate([
    //         'question_text'=>'required|unique:questions',
    //     ]);

    //     // Get the Exam record
    //     $exam = Exam::find($id);

    //     // Check if the number of existing questions is less than the allowed number
    //     if ($exam->questions()->count() < $exam->questions_number) {
    //         //object creating
    //         $question = Question::create([
    //             'question_text'=>$request->question_text,
    //             'exam_id'=>$id,
    //             'difficulty'=>$request->difficulty
    //         ]);

    //         //sending response
    //         if($question){
    //             return response()->json([
    //                 'status'=>true ,
    //                 'message'=>"تم اضافة السؤال بنجاح  "
    //             ]);
    //         }else{
    //             return response()->json([
    //                 'status'=>false ,
    //                 'message'=>"معلومات خاطئة  "
    //             ]);
    //         }
    //     } else {
    //         // Return an error response if the limit is reached
    //         return response()->json([
    //             'status'=>false ,
    //             'message'=>"لقد تجاوزت الحد المسموح به لاضافة الاسئلة "
    //         ], 422); // Use 422 Unprocessable Entity status code
    //     }
    // }

    public function setQuestion(Request $request , $id ){
        //Validations
        // $rules = [
        //     'is_correct' => 'in:true,false',
        // ];
        // $request->validate([
        //     // 'question_text'=>'required|unique:questions',
        //     'question_text' => 'required|unique:questions,question_text,NULL,id,exam_id,' . $id,
        //     'question_deg'=>'required|numeric',
        //     'option_text'=>'required|max:50',
        //     $rules
        // ]);
        $rules = [
            'options.*.option_text' => 'required|max:50', // Validate each option_text
            'options.*.is_correct' => 'in:true,false', // Validate each is_correct
        ];

        $request->validate([
            'question_text' => 'required|unique:questions,question_text,NULL,id,exam_id,' . $id,
            'question_deg' => 'required|numeric',
            'options' => 'required|array',
            $rules
        ]);

        // Get the Exam record
        $exam = Exam::find($id);

        // Check if the number of existing questions for this exam is less than the allowed number
        // if ($exam->questions()->where('exam_id', $id)->count() < $exam->qestions_number) {
        //     //object creating
        //     $question = Question::create([
        //         'question_text'=>$request->question_text,
        //         'exam_id'=>$id,
        //         'question_deg'=>$request->question_deg
        //     ]);
        //     for($i=1 ; $i>=4 ; $i++){
        //         $option = Option::create([
        //             'option_text'=>$request->option_text,
        //             'question_id'=>$question->id,
        //             'is_correct'=>$request->is_correct
        //         ]);
        //     }
        if ($exam->questions()->where('exam_id', $id)->count() < $exam->qestions_number) {
            // Create the question
            $question = Question::create([
                'question_text' => $request->question_text,
                'exam_id' => $id,
                'question_deg' => $request->question_deg
            ]);

            $existingOptionTexts = [];
            foreach ($request->options as $optionData) {
                if (in_array($optionData['option_text'], $existingOptionTexts)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'يوجد خيار مشابه . جميع الخيارات يجب ان تكون فريدة'
                    ], 422);
                }
                $existingOptionTexts[] = $optionData['option_text'];
            }
            // Loop through each option and create them
            foreach ($request->options as $optionData) {
                Option::create([
                    'option_text' => $optionData['option_text'],
                    'question_id' => $question->id,
                    'is_correct' => $optionData['is_correct']
                ]);
            }
            //sending response
            if($question){
                return response()->json([
                    'status'=>true ,
                    'message'=>"تم حفظ السؤال  "
                ]);
            }else{
                return response()->json([
                    'status'=>false ,
                    'message'=>"معلومات خاطئة  "
                ]);
            }
        } else {
            // Return an error response if the limit is reached
            return response()->json([
                'status'=>false ,
                'message'=>"قد وصلت الى الحد الاعلى المسموح به لاضافه الاسئله"
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
                'message'=>"لا يوجد اسئلة "
            ]);
        }
    }

    public function deleteَQuestion($question_id){

        $question_deleted = Question::find($question_id);

        if($question_deleted){
            $question_deleted->delete();

            return response()->json([
                'status'=>true,
                'message'=>'تم حذف السؤال بنجاح '
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>"هذا السؤال غير موجود "
            ],404);
        }

    }

    

}
