<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class I18n extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'i18ns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'resource_type',
        'resource_id',
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
        'resource_type',
        'resource_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public function i18n()
    {
        return $this->morphTo();
    }
}
