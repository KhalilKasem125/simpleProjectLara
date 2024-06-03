<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options' ;
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'created_by',
        'updated_by'
    ];

    use HasFactory;
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
