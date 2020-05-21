<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\College;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(College::class, function (Faker $faker) {
    return [
        'code' => Str::random(4),
    ];
});
