<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Video;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class VideoControlller extends Controller
{
    //Post - Admins Only
    public function insertVideo(Request $request , $id){
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                "status" => false,
                "message" => "ليس لديك الصلاحية للدخول"
            ], 401);
        }

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
        $video->created_by = $user->id ;
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

    public function deleteVideo($vid_id)
    {
        // Find the PDF object
        $vid = Video::find($vid_id);

        if (!$vid) {
            return response()->json([
                'status' => false,
                'message' => 'الفيديو ليس موجود'
            ], 404);
        }

        // Delete the PDF file from storage
        if (file_exists(public_path('uploads/' . $vid->video_file))) {
            unlink(public_path('uploads/' . $vid->video_file));
        }

        // Delete the PDF record from the database
        $vid->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الفيديو بنجاح '
        ]);
    }

}
