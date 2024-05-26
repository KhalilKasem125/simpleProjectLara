<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table ="videos";
    protected $fillable =[
        'video_file',
        'video_name',
        'subject_id'
    ];
    use HasFactory;
    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
