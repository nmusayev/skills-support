<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'profile_image' => asset('storage/' . $this->profile_image),
            'email' => $this->email,
            'linkedin_profile' => $this->linkedin_profile,
            'overall_point' => $this->overallPoint,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
