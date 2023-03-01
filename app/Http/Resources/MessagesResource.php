<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MessagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth('web')->user();

        return [
            'id' => $this->id,
            'message' => $this->message,
            'date' => (new Carbon($this->created_at))->format('Y-m-d'),
            'is_me' => $user->id == $this->user_id ? true : false
        ];
    }
}
