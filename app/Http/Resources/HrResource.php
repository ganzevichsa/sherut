<?php

namespace App\Http\Resources;

use App\Area;
use Illuminate\Http\Resources\Json\JsonResource;

class HrResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'organization_name' =>$this->organization ?  $this->organization->name : '',
            'areas' => Area::orderBy('name','ASC')->get(),
            'email' => $this->email,
            'about' => $this->about,
            'avatar' => $this->avatar ? '/users/avatars/'.$this->avatar : '',
        ];
    }
}
