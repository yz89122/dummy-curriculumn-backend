<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Teacher;
use Faker\Generator as Faker;

$factory->define(Teacher::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'code' => sprintf('%06d', rand(0, 999999)),
    ];
});
