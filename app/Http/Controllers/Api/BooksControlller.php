<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Book;
use App\Models\Subject;
use Illuminate\Http\Request;

class BooksControlller extends Controller
{
    public function storeBook(Request $request  , $id)
    {

        $request->validate([

            'book_file' => 'required|file|max:10240|mimes:pdf,docx,xlsx', // Validate as a PDF file
            'book_name' => 'required',
            'pages_number' => 'required|numeric',
            'description' => 'required'
        ]);

        $pdf_file = time() . '.' . $request->book_file->extension();
        $request->book_file->move(public_path('book_files'), $pdf_file);

        $file = new Book(); // Assuming you have a Pdf model
        $file->book_name = $request->book_name;
        $file->book_file = $pdf_file;
        $file->pages_number = $request->pages_number;
        $file->description = $request->description;
        $file->subject_id = $id ;
        $file->created_by = auth()->id;
        $file->save();

        return response()->json([
            "status" => true,
            "message" => "تم اضافة الكتاب بنجاح"
        ]);
    }

    public function getBooks($id)
    {
        $pdfs = Subject::find($id)->books;

        $pdfData = [];
        foreach ($pdfs as $pdf) {
            $pdfData[] = [
                'id' => $pdf->id ,
                'subject_id'=> $id ,
                'book_name' => $pdf->book_name,
                'description'=>$pdf->description,
                'pages_number'=>$pdf->pages_number,
                'url' => asset('book_files/' . $pdf->book_file),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'تم استرجاع الكتب بنجاح ',
            'data' => $pdfData,
        ]);
    }

    public function deleteBook($id)
    {
        // $admin_id = auth()->user()->id ;
        // $admin = Admin::find($admin_id);
        // $find = Admin::where('role','super_admin')
        // ->orwhere('created_by',$admin_id)->first();
        // if($find){

        $admin_id = auth()->user()->id ;
        $admin = Admin::find($admin_id);
        // $find = Admin::where('role','super_admin');
        // ->Subject::where('created_by',$admin_id)->first();
        $boo = Book::find($id);

        if($admin->role == 'super_admin' ||$boo->created_by == $admin_id ){
            // Find the PDF object
            $pdf = Book::find($id);

            if (!$pdf) {
                return response()->json([
                    'status' => false,
                    'message' => 'الكتاب ليس موجود'
                ], 404);
            }

            // Delete the PDF file from storage
            if (file_exists(public_path('book_files/' . $pdf->book_file))) {
                unlink(public_path('book_files/' . $pdf->book_file));
            }

            // Delete the PDF record from the database
            $pdf->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الكتاب بنجاح '
            ]);

        }else{
            return response()->json([
                'status' => false,
                'message' => 'ليس لديك الصلاحية للقيام بذلك '
            ],402);
        }

    }
}
