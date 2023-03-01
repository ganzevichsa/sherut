<?php

namespace App\Http\Resources;

use App\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewEditJobMidrashaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
        $hr = $this->hr()->first();

        foreach($this->images()->get() as $image) {
            if(!empty($image->file)) {
                $images[] = '/storage/jobs/'.$image->file;
            }
        }

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'video_url' => $this->video_url ? $this->video_url : false,
            'description' => $this->description,
            'images' => $images,
            'other_hr_name' => $hr->name,
            'other_hr_phone' => $hr->phone,
            'area' => $this->city ? $this->city->area : '',
            'address' => $this->address ? $this->address->name : '',
            'city' => $this->city ? $this->city : '',
            'place' => $this->place,
            'program' => $this->program,
        ];
        if ($user->role_id == Role::HR) {
            $data["statuses"] = [
                "סגור להרשמה",
                "פתוח להרשמה"
            ];
        }
        return $data;
    }
}
