<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    //

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function questions() {
        return $this->belongsToMany(Question::class);
    }
}
