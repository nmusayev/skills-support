<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    //
    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
