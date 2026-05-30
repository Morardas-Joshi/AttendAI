<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceEncoding extends Model
{
    protected $table = 'face_encodings';
    protected $fillable = [
        'student_id',
        'encoding_data',
    ];
}