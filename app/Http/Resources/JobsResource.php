<?php

namespace App\Http\Resources;

use App\Role;
use App\UserJob;
use Illuminate\Http\Resources\Json\JsonResource;

class JobsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $user = auth('web')->user();

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'logo' => $this->organization ? ($this->organization->logo ? '/storage/organizations/logos/'.$this->organization->logo : false) : false,
            'organization' => $this->organization ? $this->organization->name : '',
            'cover_image' => $this->images()->count() ? '/storage/jobs/'.$this->images()->first()->file : false,
            'organization_id' => $this->organization_id,
            'stars' => $this->reviews()->count() ? round($this->reviews()->sum('stars') / $this->reviews()->count()) : 0,
            'is_favorite' => $user->favorites()->where('job_id',$this->id)->count() ? true : false,
            'count_of_all_positions' => $this->count_of_all_positions,
            'year' => $this->year,
            'count_of_faqs' => $this->faqs()->count(),
            'count_of_taken_positions' => $this->count_of_taken_positions,
            'status' => $this->status,
            'last_date_for_registration' => $this->last_date_for_registration,
            // 'description' => $this->description,
            'description' => mb_strimwidth($this->description, 0, 200, "..."),
            'is_requested' => auth('web')->user() ? (auth('web')->user()->opportunities()->where('job_id',$this->id)->first() ? true : false) : false,
        ];
        if($user->role_id == Role::HR) {
            $data['statuses'] = [
                "סגור להרשמה",
                "פתוח להרשמה"
            ];
            $data['status'] = $data['statuses'][$this->status];
            $data['views'] = $this->views;
            $data['apply_count'] = $this->jobUsers()->where('status',UserJob::APPLY)->count();
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
