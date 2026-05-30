<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $fillable = [
        'subject_name',
        'class_id',
        'faculty_id'
    ];

    // Relationship with Class
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // Relationship with Faculty
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    // Attendance Sessions
    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }
}