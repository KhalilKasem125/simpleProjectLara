<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\File;
use App\Models\Subject;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class FilesControlller extends Controller
{
    public function storePdf(Request $request  , $id)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                "status" => false,
                "message" => "ليس لديك الصلاحية للدخول"
            ], 401);
        }

        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,docx,xlsx', // Validate as a PDF file
            'file_name' => 'required',
        ]);

        $pdf_file = time() . '.' . $request->file->extension();
        $request->file->move(public_path('files'), $pdf_file);

        $file = new File(); // Assuming you have a Pdf model
        $file->file_name = $request->file_name;
        $file->file = $pdf_file;
        $file->subject_id = $id ;
        $file->created_by = $user->id ;
        $file->save();

        return response()->json([
            "status" => true,
            "message" => "تم اضافة الملف بنجاح "
        ]);
    }

    public function getPdf($id)
    {

        $pdfs = Subject::find($id)->files;

        $pdfData = [];
        foreach ($pdfs as $pdf) {
            $pdfData[] = [
                'id' => $pdf->id ,
                'subject_id'=> $id ,
                'name' => $pdf->file_name,
                'url' => asset('files/' . $pdf->file),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'تم استرجاع الملف بنجاح',
            'data' => $pdfData,
        ]);
    }

    public function deletePdf($id)
    {
        $admin_id = auth()->user()->id ;
        $admin = Admin::find($admin_id);
        // $find = Admin::where('role','super_admin');
        // ->Subject::where('created_by',$admin_id)->first();
        $sup = File::find($id);

        if($admin->role == 'super_admin' || $sup->created_by == $admin_id ){

            // Find the PDF object
            $pdf = File::find($id);

            if (!$pdf) {
                return response()->json([
                    'status' => false,
                    'message' => 'الملف غير موجود'
                ], 404);
            }

            // Delete the PDF file from storage
            if (file_exists(public_path('files/' . $pdf->file))) {
                unlink(public_path('files/' . $pdf->file));
            }

            // Delete the PDF record from the database
            $pdf->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الملف بنجاح'
            ]);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'ليس لديك الصلاحية للقيام بذلك '
            ],402);
        }
    }
}
