<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exams_result extends Model
{
    protected $table = 'Exams_result' ;
    protected $fillable = [
        'exam_id',
        'user_id',
        'score',
        'finished_at',
        'status'
    ];
    protected $dates = [

        'finished_at'
    ];
    protected $casts = [

        'finished_at' => 'datetime',
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    use HasFactory;
}


