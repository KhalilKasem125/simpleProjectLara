<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoControlller extends Controller
{
    //Post - Admins Only
    public function insertVideo(Request $request , $id){
        $request->validate([
            'video_file'=>'required|file|mimetypes:video/mp4,video/quicktime|max:30000',
            'video_name'=>'required',
        ]);
        $video_file =  time() . '.' . $request->video_file->extension();
        $request->video_file->move(public_path('uploads'), $video_file);

        $video = new Video();
        $video->video_name = $request->video_name;
        $video->video_file = $video_file;
        $video->subject_id = $id ;
        $video->save();

        return response()->json([
            "status"=>true,
            "message"=>"تم اضافة الفيديو بنجاح "
        ]);
    }

    public function getVideos($id)
    {

        // Fetch videos (adjust query as needed)
        $videos = Subject::find($id)->videos;

        // Prepare data for API response
        $videoData = [];
        foreach ($videos as $video) {
            $videoData[] = [
                'id' => $video->id,
                'subject_id'=>$id,
                'name' => $video->video_name,
                'url' => asset('uploads/' . $video->video_file), // Generate URL for video file
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'تم استرجاع الفيديوهات',
            'data' => $videoData,
        ]);
    }

}
