<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
class OpportunityContender extends JsonResource
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
            'job_id' => $this->job_id,
            'job_name' => $this->job->title,
            'phone' => $this->user->phone,
            'city' => 'אין רישום',
            'birthdate' => $this->user->birthdate,
            'email' => $this->user->email,
            'status' => $this->status,
            'statuses' => [
                'העברה לרשימת המתנה',
                'התקבלה',
                'העברה לרשימת המתנה',
            ]
        ];
    }
}
