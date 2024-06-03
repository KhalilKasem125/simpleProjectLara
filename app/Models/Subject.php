<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{

    protected $table = "subjects";

    protected $fillable =[
        "subject_name",
        'created_by',
        'updated_by'
    ];
    use HasFactory;


    public function teachers(){
        return $this->hasMany(Teacher::class);
    }
    public function books(){
        return $this->hasMany(Book::class);
    }
    public function videos(){
        return $this->hasMany(Video::class);
    }
    public function photos(){
        return $this->hasMany(Photo::class);
    }
    public function files(){
        return $this->hasMany(File::class);
    }
    public function exams(){
        return $this->hasMany(Exam::class);
    }
}
