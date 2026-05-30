<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{

    protected $table = 'faculty';
    protected $fillable = [
        'user_id',
        'name',
        'employee_id',
        'department',
        'profile_photo'
    ];

public function user()
{
    return $this->belongsTo(User::class);
}

public function subjects()
{
    return $this->hasMany(Subject::class);
}

public function sessions()
{
    return $this->hasMany(AttendanceSession::class);
}
}
