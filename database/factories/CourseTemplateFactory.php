<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CourseTemplate;
use App\Models\I18n;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(CourseTemplate::class, function (Faker $faker) {
    return [
        'code' => Str::random(5),
    ];
});

$factory->afterCreating(CourseTemplate::class, function (CourseTemplate $template, Faker $faker) {
    $template->i18n()->save(factory(I18n::class)->make(['text' => $faker->city]));
});
