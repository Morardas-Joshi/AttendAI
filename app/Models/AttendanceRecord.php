<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{

    protected $table = 'attendance_records';
    protected $fillable = [
    'student_id',
    'session_id',
    'status',
    'confidence_score',
    'ip_address',
    'marked_at',
    'student_lat',
    'student_lng',
    'distance_meters',
    'liveness_passed',
    'rejection_reason'
];

    protected $casts = [
        'marked_at' => 'datetime',
        'confidence_score' => 'float',
        'student_lat' => 'float',
        'student_lng' => 'float',
        'distance_meters' => 'float',
        'liveness_passed' => 'boolean',
    ];
 public $timestamps = true; 

public function student()
{
    return $this->belongsTo(Student::class);
}

public function session()
{
    return $this->belongsTo(AttendanceSession::class, 'session_id');
}
}
