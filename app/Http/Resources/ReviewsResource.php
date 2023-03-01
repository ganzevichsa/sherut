<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewsResource extends JsonResource
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
            'name' => $this->user->name,
            'avatar' => '/users/avatars/'.$this->user->avatar,
            'created_at' => new Carbon($this->created_at),
            'stars' => $this->stars,
            'description' => $this->who_you_are . "\n" . $this->who_you_think
        ];
    }
}
