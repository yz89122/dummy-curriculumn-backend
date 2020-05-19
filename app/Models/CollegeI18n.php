<?php

namespace App\Models;

class CollegeI18n extends BaseI18n
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'college_i18ns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'college_id',
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
        'college_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
}
