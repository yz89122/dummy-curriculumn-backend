<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\I18n;
use Faker\Generator as Faker;

$factory->define(I18n::class, function (Faker $faker) {
    return [
        'locale' => 'default',
    ];
});
