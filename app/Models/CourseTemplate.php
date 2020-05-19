<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTemplate extends BaseModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
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

    public function names()
    {
        return $this->hasMany(CourseName::class, 'course_template_id', 'id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_template_id', 'id');
    }
}
