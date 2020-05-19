<?php

namespace App\Models;

class Course extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'course_template_id',
        'academic_year',
        'academic_term',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'course_template_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function course_template()
    {
        return $this->belongsTo(CourseTemplate::class, 'course_template_id', 'id', 'course_template');
    }

    public function course_times()
    {
        return $this->hasMany(CourseTime::class, 'course_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'courses_teachers', 'course_id', 'teacher_id', 'id', 'id', 'teacher');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'selections', 'course_id', 'student_id', 'id', 'id', 'student')
            ->wherePivot('deleted_at', '=', null);
    }
}
