<?php

namespace App\Http\Resources;

use App\UserJob;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
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
            'title' => $this->title,
            'logo' => $this->organization ? ($this->organization->logo ? '/storage/organizations/logos/'.$this->organization->logo : false) : false,
            'organization_name' => $this->organization ? $this->organization->name : '',
            'cover_image' => $this->images()->count() ? '/storage/jobs/'.$this->images()->first()->file : false,
            "statuses" => [
                "סגור להרשמה",
                "פתוח להרשמה"
            ],
            'status' => $this->status,
            "date" => new Carbon($this->created_at),
            'count_of_all_positions' => $this->count_of_all_positions,
            'count_of_taken_positions' => $this->count_of_taken_positions,
            'description' => $this->description
        ];
    }
}
