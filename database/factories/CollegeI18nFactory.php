<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\College;
use App\Models\CollegeI18n;
use Faker\Generator as Faker;

$factory->define(CollegeI18n::class, function (Faker $faker) {
    return [
        'locale' => 'default',
        'text' => $faker->country,
    ];
});

$factory->afterCreating(College::class, function (College $college, Faker $faker) {
    $college->i18n()->save(factory(CollegeI18n::class)->make());
});
