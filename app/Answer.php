<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //
    protected $guarded = [];
    protected $appends = ['voteSum'];
    protected $hidden = ['question', 'user', 'votes', 'voteUsers', 'voteSum'];

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function voteUsers() {
        return $this->belongsToMany(User::class, 'votes')
            ->withTimestamps();
    }

    public function getVoteSumAttribute() {
        return $this->votes->sum('value');
    }
}
