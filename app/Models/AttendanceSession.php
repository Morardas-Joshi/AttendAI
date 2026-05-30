<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $table = 'attendance_sessions';

    protected $fillable = [
    'subject_id',
    'faculty_id',
    'start_time',
    'end_time',
    'session_code',
    'status',
    'allowed_ip_range',
    'classroom_lat',
    'classroom_lng',
    'radius_meters'
];

public function subject()
{
    return $this->belongsTo(Subject::class);
}

public function faculty()
{
    return $this->belongsTo(Faculty::class);
}

public function records()
{
    return $this->hasMany(AttendanceRecord::class, 'session_id');
}
}
