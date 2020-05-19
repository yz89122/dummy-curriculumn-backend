<?php

namespace App\Models;

class DepartmentI18n extends BaseI18n
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'department_i18ns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department_id',
        'locale',
        'text',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'department_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
}
