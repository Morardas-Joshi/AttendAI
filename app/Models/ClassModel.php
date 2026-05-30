<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';

protected $fillable = [
    'course_name',
    'year',
    'semester',
    'division'
];

public function students()
{
    return $this->hasMany(Student::class);
}

public function subjects()
{
    return $this->hasMany(Subject::class);
}
}
