<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LanguageTableSeeder::class);
        $this->call(SkillsTableSeeder::class);

//        factory(\App\Skill::class, 1000)->create();

        factory(\App\User::class, 50)->create()->each(function($user) {
            $user->skills()->saveMany(\App\Skill::all()->random(rand(1, 25)));
            $user->languages()->saveMany(\App\Language::all()->random(rand(1, 10)));
            $user->save();
        });

        factory(\App\Question::class, 2000)->create()->each(function($question) {
            $question->skills()->saveMany(\App\Skill::all()->random(rand(1, 10)));
            $question->save();
        });

        factory(\App\Answer::class, 4000)->create();
        factory(\App\Vote::class, 1000)->create();
    }
}
