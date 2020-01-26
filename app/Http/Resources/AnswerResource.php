<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "content" => $this->content,
            "user" => $this->user,
            "is_best" => $this->is_best,
            "votes" => $this->votes->sum('value'),
            "question" => $this->question,
            "vote_up_users" => $this->votes->where('value', 1)->pluck('user_id')->toArray(),
            "vote_down_users" => $this->votes->where('value', -1)->pluck('user_id')->toArray(),
            "created_at" => $this->created_at->diffForHumans(),
            "updated_at" => $this->updated_at->diffForHumans(),
        ];
    }
}
