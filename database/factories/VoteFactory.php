<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Vote::class, function (Faker $faker) {
    return [
        //
        'value' => rand(0,1),
        'user_id' => \App\User::all()->random()->id,
        'answer_id' => \App\Answer::all()->random()->id,
    ];
});
