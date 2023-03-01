<?php

namespace App\Http\Resources;

use App\Role;
use Illuminate\Http\Resources\Json\JsonResource;
use App\UserJob;

class SingleJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hr = $this->hr()->first();
        $images = [];
        foreach($this->images()->get() as $image) {
            if(!empty($image->file)) {
                $images[] = '/storage/jobs/'.$image->file;
            }
        }
        $where_we_live = [];
        if($this->home > 0) {
            $where_we_live['home'] = 'תקן בית';
        }
        if($this->out > 0) {
            $where_we_live['out'] = 'תקן דירה';
        }
        if($this->dormitory > 0) {
            $where_we_live['dormitory'] = 'פנימיה';
        }

        if(isset($this->organization)){
            $organization_logo = $this->organization->logo;
            $organization_name = $this->organization->name;
            $organization_id = $this->organization->id;

        }
        else{
            $organization_logo = "";
            $organization_name = "";
            $organization_id = "";
        }
        $user = auth('web')->user();
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'category' => new SimpleTableResource($this->category),
            'cover_image' => $this->images()->count() ? '/storage/jobs/'.$this->images()->first()->file : false,
            'logo' => $organization_logo,
            'category_name' => $this->category ? $this->category->name : '',
            'subcategory_name' => $this->subcategory ? $this->subcategory->name : '',
            'area_name' => $this->city ? $this->city->area->name : '',
            'city_name' => $this->city ? $this->city->name : '',
            'where_we_live' => $where_we_live,
            'address' => $this->address ? $this->address->name : '',
            'organization_id' => $organization_id,
            'organization_name' => $organization_name,
            'nucleus' => $this->nucleus,
            'how_to_sort' => $this->how_to_sort,
            'hr_name' => $hr->name,
            'hr_phone' => $hr->phone,
            'hr_email' => $hr->email,
            'images' => $images,
            'video_url' => $this->video_url ? $this->video_url : false,
            'stars' => $this->reviews()->count() ? round($this->reviews()->sum('stars') / $this->reviews()->count()) : 0,
            'count_of_all_positions' => $this->count_of_all_positions,
            // 'count_of_taken_positions' => $this->jobUsers->where('status', 1)->count(),
            'count_of_taken_positions' => $this->count_of_taken_positions,

            'count_job_users' => $this->count_job_users,
            'count_job_reviews' => $this->reviews()->count(),
            'last_date_for_registration' => $this->last_date_for_registration,
            'is_requested' => auth('web')->user() ? (auth('web')->user()->opportunities()->where('job_id',$this->id)->first() ? true : false) : false,
            'description' => $this->description,
            'is_favorite' => $this->is_favorite,
            'count_job_users' => $this->jobUsers->count(),
            
        ];
        if ($user->role_id == Role::HR) {
            $data["statuses"] = [
                "סגור להרשמה",
                "פתוח להרשמה"
            ];
        }
        $user_job = $this->jobUsers()->where('user_id', $user->id)->first();

        if(isset($user_job)){
            $data['status_user_job'] = $user_job['status'] == UserJob::APPLY ? 'העברה לרשימת המתנה' : ($user_job['status'] == UserJob::APPROVED ? 'התקבלה' : ($user_job['status'] == UserJob::CANCEL ? 'העברה לרשימת המתנה' : false));
        }else{
            $data['status_user_job'] = false;
        }
        
        return $data;
    }
}
