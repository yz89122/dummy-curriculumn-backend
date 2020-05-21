<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Department;
use App\Models\DepartmentI18n;
use Faker\Generator as Faker;

$factory->define(DepartmentI18n::class, function (Faker $faker) {
    return [
        'locale' => 'en',
        'text' => $faker->state,
    ];
});

$factory->afterCreating(Department::class, function (Department $department, Faker $faker) {
    $department->i18n()->save(factory(DepartmentI18n::class)->make());
});
