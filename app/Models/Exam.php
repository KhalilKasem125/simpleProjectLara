<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = "exams";

    protected $fillable = [
        'subject_id',
        'qestions_number',
        'success_degree',
        'Exam_Name',
        'exam_day_start_point',
        'exam_day_end_point',
        'exam_time'
    ] ;
    protected $dates = [
        'exam_day_start_point',
        'exam_day_end_point'
    ];
    protected $casts = [
        'exam_day_start_point' => 'datetime',  // Cast to Carbon instances
        'exam_day_end_point' => 'datetime',
    ];
    use HasFactory;

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function exams_result()
    {
        return $this->hasMany(Exams_result::class);
    }
}
