<?php

namespace App\Http\Resources;

use App\UserJob;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileRequestsResource extends JsonResource
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
            'title' => $this->job->title,
            'organization_name' => $this->job->organization ? $this->job->organization->name : '',
            'logo' => $this->organization ? $this->organization->logo ? '/storage/organizations/logos/'.$this->organization->logo : false : '',
            'status' => $this->status,
            'status_text' => $this->status == UserJob::APPLY ? 'העברה לרשימת המתנה' : ($this->status == UserJob::APPROVED ? 'התקבלה' : ($this->status == UserJob::CANCEL ? 'העברה לרשימת המתנה' : '')),
            'send_again' => $this->status == UserJob::APPLY && new Carbon($this->updated_at) < Carbon::now()->subDays(2) ? true : false,
        ];
    }
}
