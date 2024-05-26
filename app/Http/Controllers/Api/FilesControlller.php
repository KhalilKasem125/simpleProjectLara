<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Subject;
use Illuminate\Http\Request;

class FilesControlller extends Controller
{
    public function storePdf(Request $request  , $id)
    {

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
        $file->save();

        return response()->json([
            "status" => true,
            "message" => "The PDF has been saved successfully"
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
            'message' => 'PDFs retrieved successfully',
            'data' => $pdfData,
        ]);
    }

    public function deletePdf($id)
    {
        // Find the PDF object
        $pdf = File::find($id);

        if (!$pdf) {
            return response()->json([
                'status' => false,
                'message' => 'PDF not found.'
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
            'message' => 'PDF deleted successfully.'
        ]);
    }
}
