<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Translation;
use Faker\Generator as Faker;

$factory->define(Translation::class, function (Faker $faker) {
    return [
        'term_raw' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'url_raw' => $faker->url,
    ];
});
