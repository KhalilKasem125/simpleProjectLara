<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = "teachers";
    protected $fillable = [
        "first_name",
        "last_name",
        "phone_no",
        "description",
        "teaching_duration",
        "date_of_birth",
        "subject_name",
        'subject_id'
    ];
    use HasFactory;
    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
