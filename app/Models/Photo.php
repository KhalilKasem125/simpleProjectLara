<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = "photos";
    protected $fillable = [
        'photo_name',
        'photo_file',
        'subject_id'
    ];
    use HasFactory;
    
    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
