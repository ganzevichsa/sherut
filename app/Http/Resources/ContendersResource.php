<?php

namespace App\Http\Resources;

use App\User;
use App\UserJob;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ContendersResource extends JsonResource
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
            'name' => !empty($this->user->first_name) ? $this->user->first_name . " " . $this->user->last_name : $this->user->name,
            'avatar' => $this->user->avatar ? '/users/avatars/' . $this->user->avatar : '',
            'date' => new Carbon($this->created_at),
            'status' => $this->status == UserJob::APPLY ? 'העברה לרשימת המתנה' : ($this->status == UserJob::APPROVED ? 'התקבלה' : ($this->status == UserJob::CANCEL ? 'העברה לרשימת המתנה' : '')),
            'city' => 'אין רישום',
            'phone' => $this->user->phone
        ];
    }
}
