<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityEditMidrashaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $images = [];
        foreach($this->images()->get() as $image) {
            $images[] = '/storage/jobs/'.$image->path;
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'area_id' => $this->city ? $this->city->area->id : null,
            'city_id' => $this->city ? $this->city_id : null,
            'address' => $this->address ? $this->address->name : '',
            'program' => $this->program,
            'place' => $this->home > 0 ? 'home' : ($this->out > 0 ? 'out' : ($this->dormitory > 0 ? 'dormitory' : '')),
            'route' => $this->route_midrasha,
            'target_audience' => $this->target_audience,
            'main_areas_of_study' => $this->main_areas_of_study,
            'description' => $this->description,
            'images' => $images,
            'video_url' => $this->video_url,
            'count_of_all_positions' => $this->count_of_all_positions,
            'other_hr_name' => $this->other_hr_name ? $this->other_hr_name : '',
            'other_hr_phone' => $this->other_hr_phone ? $this->other_hr_phone : '',
        ];
    }
}
