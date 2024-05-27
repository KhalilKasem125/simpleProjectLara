<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudyingPlaneControlller extends Controller
{
    public function insertPhoto(Request $request , $id)
    {
        $request->validate([
            'photo_file' =>  'required|image|mimes:jpeg,png,jpg,gif|max:4000', // Validate as an image
            'photo_name' => 'required',
        ]);

        $photo_file = time() . '.' . $request->photo_file->extension();
        $request->photo_file->move(public_path('images'), $photo_file);

        $photo = new Photo();
        $photo->photo_name = $request->photo_name;
        $photo->photo_file = $photo_file;
        $photo->subject_id = $id ;
        $photo->save();


        return response()->json([
            "status" => true,
            "message" => "تم اضافه الخطة بنجاح "
        ]);
    }

    public function getPhotos($id)
    {
        $photos = Subject::find($id)->photos;

        $photoData = [];
        foreach ($photos as $photo) {
            $photoData[] = [
                'id' => $photo->id,
                'subject_id'=>$id,
                'name' => $photo->photo_name,
                'url' => asset('images/' . $photo->photo_file),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'تم استرجاع الخطة بنجاح ',
            'data' => $photoData,
        ]);
    }
}
