<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = "files";
    protected $fillable = [
        'file_name',
        'file',
        'subject_id',
        'created_by',
        'updated_by'
    ];
    use HasFactory;
    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
