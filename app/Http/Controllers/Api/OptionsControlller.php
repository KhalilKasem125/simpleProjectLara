<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OptionsControlller extends Controller
{
    public function setOption(Request $request , $id )
    {
        //Validations
        //also we have difficulty and its an enumeration between  three choices (hard ,easy ,medium)
        //but this can be nullable so we dont want to validate to be optionally added
        $rules = [
            'is_correct' => 'in:true,false',
        ];
        // $request->validate([
        //     'option_text'=>'required|max:50',
        //     'is_correct'=>''
        // ]);
        $request->validate([
            'option_text'=>'required|max:50',
            $rules
        ]);

        //object creating
        $option = Option::create([
            'option_text'=>$request->option_text,
            'question_id'=>$id,
            'is_correct'=>$request->is_correct
        ]);

        //sending response
        if($option){
            return response()->json([
                'status'=>true ,
                'message'=>"تم حفظ الخيار بنجاح  "
            ]);
        }else{
            return response()->json([
                'status'=>false ,
                'message'=>"معلومات خاطئة  "
            ]);
        }

    }

    public function getOptions($id)
    {


        $options = Question::find($id)->options;

        if($options){
            return response()->json([
                'status'=>true ,
                'Questions'=>$options,

            ]);
        }else{
            return response()->json([
                'status'=>false ,
                'message'=>"لا يوجد خيارات "
            ]);
        }
    }


}
