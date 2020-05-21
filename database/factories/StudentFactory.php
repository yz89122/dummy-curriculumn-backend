<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Student;
use Faker\Generator as Faker;

$factory->define(Student::class, function (Faker $faker) {
    $grade = rand(0, 5);
    return [
        'user_id' => factory(User::class)->create()->id,
        'code' => sprintf('%09d', rand(0, 999999999)),
        'grade' => Student::GRADE[$grade],
        'registered_year' => now()->year - $grade,
    ];
});
