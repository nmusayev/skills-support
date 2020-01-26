<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Question::class, function (Faker $faker) {
    return [
        //
        'title' => $faker->sentence,
        'content' => $faker->text,
        'user_id' => \App\User::all()->random()->id,
        'language_id' => \App\Language::all()->random()->id,
    ];
});
