<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Department;
use App\Models\College;
use App\Models\I18n;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'code' => Str::random(4),
    ];
});

$factory->afterCreating(Department::class, function (Department $department, Faker $faker) {
    $department->i18n()->save(factory(I18n::class)->make(['text' => $faker->state]));
});
