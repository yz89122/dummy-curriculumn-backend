<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\College;
use App\Models\I18n;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(College::class, function (Faker $faker) {
    return [
        'code' => Str::random(4),
    ];
});

$factory->afterCreating(College::class, function (College $college, Faker $faker) {
    $college->i18n()->save(factory(I18n::class)->make(['text' => $faker->country]));
});
