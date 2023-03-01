<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityEditResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
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
            'category_id' => $this->category ? $this->category_id : null,
            'subcategory_id' => $this->subcategory ? $this->subcategory_id : null,
            'organization_id' => $this->organization ? $this->organization_id : null,
            'route_id' => $this->organizationRoute()->count() ? $this->organizationRoute()->pluck('id') : [],
            'job_for' => $this->job_for,
            'description' => $this->description,
            'area_id' => $this->city ? $this->city->area->id : null,
            'city_id' => $this->city ? $this->city_id : null,
            'address' => $this->address ? $this->address->name : '',
            'place' => $this->home > 0 ? 'home' : ($this->out > 0 ? 'out' : ($this->dormitory > 0 ? 'dormitory' : '')),
            'nucleus' => $this->nucleus,
            'count_of_all_positions' => $this->count_of_all_positions,
            'how_to_sort' => $this->how_to_sort,
            'images' => $images,
            'video_url' => $this->video_url ? $this->video_url : '',
            'last_date_for_registration' => $this->last_date_for_registration ? [
                'day' => (new Carbon($this->last_date_for_registration))->format('d'),
                'month' => (new Carbon($this->last_date_for_registration))->format('m'),
                'year' => (new Carbon($this->last_date_for_registration))->format('Y')
            ] : [
                'day' => null,
                'month' => null,
                'year' => null
            ],
            'other_hr_name' => $this->other_hr_name ? $this->other_hr_name : '',
            'other_hr_phone' => $this->other_hr_phone ? $this->other_hr_phone : '',
        ];
    }
}
