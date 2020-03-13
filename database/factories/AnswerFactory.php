<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Answer;
use Faker\Generator as Faker;

$factory->define(Answer::class, function (Faker $faker) {
    return [
        'created_at' => $faker->dateTimeThisYear($max = 'now', $timezone = null),
        'updated_at' => now(),
    ];
});
