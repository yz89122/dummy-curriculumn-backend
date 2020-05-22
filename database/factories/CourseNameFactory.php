<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CourseTemplate;
use App\Models\CourseName;
use Faker\Generator as Faker;

$factory->define(CourseName::class, function (Faker $faker) {
    return [
        'locale' => 'default',
        'text' => $faker->city,
    ];
});

$factory->afterCreating(CourseTemplate::class, function (CourseTemplate $template, Faker $faker) {
    $template->names()->save(factory(CourseName::class)->make());
});
