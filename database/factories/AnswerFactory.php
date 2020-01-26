<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Answer::class, function (Faker $faker) {
    return [
        //
        'content' => $faker->text,
        'user_id' => \App\User::all()->random()->id,
        'question_id' => \App\Question::all()->random()->id,
    ];
});
