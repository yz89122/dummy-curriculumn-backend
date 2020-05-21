<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CourseTemplate;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(CourseTemplate::class, function (Faker $faker) {
    return [
        'code' => Str::random(5),
    ];
});
