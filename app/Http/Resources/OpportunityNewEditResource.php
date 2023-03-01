<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityNewEditResource extends JsonResource
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
            $images[] = '/storage/jobs/'.$image->file;
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category ? $this->category : null,
            'subCategory' => $this->subcategory ? $this->subcategory : null,
            'organization' => $this->organization ? $this->organization : null,
            'route_id' => $this->organizationRoute()->count() ? $this->organizationRoute()->get() : [],
            'job_for' => $this->job_for,
            'description' => $this->description,
            'area' => $this->city ? $this->city->area : null,
            'city_id' => $this->city ? $this->city : null,
            'address' => $this->address ? $this->address->name : '',
            'place' => $this->place == 'home'  ? [
                'id' => 'home',
                'name' => 'תקן בית'
            ] : ($this->place == 'out' ? [
                'id' => 'out',
                'name' => 'דירת שירות'
            ] : [
                'id' => 'dormitory',
                'name' => 'פנימיה'
            ]),
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
            'isMidrashot' => false,
        ];
    }
}
