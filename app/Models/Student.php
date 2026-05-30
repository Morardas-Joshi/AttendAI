<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $fillable = [
    'user_id',
    'roll_no',
    'class_id'
];

public function user()
{
    return $this->belongsTo(User::class);
}

public function class()
{
    return $this->belongsTo(ClassModel::class, 'class_id');
}

public function faceEncoding()
{
    return $this->hasOne(FaceEncoding::class);
}

public function attendanceRecords()
{
    return $this->hasMany(AttendanceRecord::class);
}
}
