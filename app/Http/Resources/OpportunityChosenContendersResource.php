<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
class OpportunityChosenContendersResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => !empty($this->user->first_name) ? $this->user->first_name . " " . $this->user->last_name : $this->user->name,
            'phone' => $this->user->phone,
            'avatar' => $this->user->avatar ? '/users/avatars/' . $this->user->avatar : '',
            'date' => new Carbon($this->created_at),
            'status' => 'העברה לרשימת המתנה',
            'city' => 'אין רישום',
            'about' => $this->user->about,
            'count_opportunities' => $this->user->opportunities->count(),
            'jobs_name' => $this->job->title
        ];
    }
}
