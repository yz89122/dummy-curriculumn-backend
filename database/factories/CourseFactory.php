<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Course;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'code' => Str::random(5),
        'academic_year' => now()->year,
        'academic_term' => collect(Course::ACADEMIC_TERM)->random(),
    ];
});
