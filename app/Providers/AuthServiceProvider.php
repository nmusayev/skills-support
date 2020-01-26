<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-delete-question', function ($user, $question) {
            return $user->id == $question->user_id;
        });

        Gate::define('update-delete-answer', function ($user, $answer) {
            return $user->id == $answer->user_id;
        });

        Gate::define('make-answer-best', function ($user, $answer) {
            return $user->id == $answer->question->user_id && $user->id != $answer->user_id;
        });

        //
        Passport::routes();
    }
}
