<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends BaseModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'college_id',
        'code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'college_id',
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

        static::deleted(function (Model $model) {
            if ($model->isForceDeleting()) {
                $model->i18n()->delete();
            }
        });
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'id', 'college');
    }

    public function i18n()
    {
        return $this->morphMany(I18n::class, 'resource', 'resource_type', 'resource_id', 'id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'department_id', 'id');
    }
}
