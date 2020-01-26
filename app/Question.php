<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function language() {
        return $this->belongsTo(Language::class);
    }

    public function skills() {
        return $this->belongsToMany(Skill::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }
}
