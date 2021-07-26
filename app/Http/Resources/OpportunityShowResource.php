<?php

namespace App\Http\Resources;

use App\Http\Controllers\HrController;
use App\Http\Controllers\HrJobController;
use App\JobType;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->hr()->first();
        $is_edit = false;
        if ($job = auth('web')->user()->jobs()->where('jobs.id', $this->id)->first()) {
            $is_edit = true;
        }
        $hrController = new HrJobController();
        if($this->job_type_id == JobType::MIDRASHA) {
            $places = $hrController->midrasha_places;
        } else {
            $places = $hrController->places;
        }
        return [
            'id' => $this->id,
            'is_midrasha' => $this->job_type_id == JobType::MIDRASHA ? true : false,
            'title' => $this->title,
            'logo' => $this->organization ? ($this->organization->logo ? '/storage/organizations/logos/'.$this->organization->logo : false) : '',
            'organization_name' => $this->organization ? $this->organization->name : '',
            "statuses" => [
                "סגור להרשמה",
                "פתוח להרשמה"
            ],
            'status' => $this->status,
            'type' => $this->type->name,
            'category_id' => $this->category ? $this->category_id : '',
            'subcategory_id' => $this->subcategory ? $this->subcategory_id : '',
            'name' => $this->title,
            'description' => $this->description,
            'video_url' => $this->video_url,
            'area' => $this->city ? $this->city->area->name : '',
            'city' => $this->city ? $this->city->name : '',
            'place' => $this->home > 0 ? $places['home'] : ($this->out > 0 ? $places['out'] : ($this->dormitory > 0 ? $places['dormitory'] : '')),
            'count' => $this->home + $this->dormitory + $this->out,
            'nucleus' => $this->nucleus,
            'how_to_sort' => $this->how_to_sort,
            'other_hr_name' => $this->other_hr_name,
            'phone' => $user->phone,
            'email' => $user->email,
            'other_hr_phone' => $this->other_hr_phone,
            'is_edit' => $is_edit
        ];
    }
}
