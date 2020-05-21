<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Department;
use App\Models\College;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'code' => Str::random(4),
    ];
});
