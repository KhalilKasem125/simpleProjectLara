<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = "books";
    protected $fillable = [
        'book_name',
        'book_file',
        'pages_number',
        'description',
        'subject_id',
        'created_by',
        'updated_by'
    ];
    use HasFactory;
    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
