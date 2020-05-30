<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Administrator;
use Faker\Generator as Faker;

$factory->define(Administrator::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'code' => sprintf('%04d', rand(0, 9999)),
    ];
});
