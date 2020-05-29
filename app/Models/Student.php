<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends BaseModel
{
    use SoftDeletes;

    public const GRADE = [
        null,
        'Freshman',
        'Sophomore',
        'Junior',
        'Senior',
        'Graduate',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'user_id',
        'department_id',
        'registered_year',
        'grade',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'user_id',
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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'user');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id', 'department');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'selections', 'student_id', 'course_id', 'id', 'id', 'course')
            ->wherePivot('deleted_at', '=', null);
    }
}
