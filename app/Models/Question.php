<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions' ;
    protected $fillable = [
        'exam_id',
        'question_text',
        'question_deg',
        'created_by',
        'updated_by'
    ];

    public function options(){
        return $this->hasMany(Option::class);
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    use HasFactory;
}
