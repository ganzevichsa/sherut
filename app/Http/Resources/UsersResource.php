<?php

namespace App\Http\Resources;

use App\Role;
use Illuminate\Http\Resources\Json\JsonResource;
use App\School;

class UsersResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        $birthdate = null;
        if(!empty($this->birthdate_hebrew)) {
            $birthdate = explode('-',$this->birthdate_hebrew);
            $birthdate_text = $this->birthdate_hebrew;
        } elseif(!empty($this->birthdate) ){
            $birthdate = explode('-',$this->birthdate);
            $birthdate_text = $this->birthdate;
        }
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthdate_array' => $birthdate ? [
                'year' => $birthdate[0],
                'month' => $birthdate[1],
                'day' => $birthdate[2]
            ] : null,
            'birthdate' => $birthdate ? $birthdate_text : null,
            'organization_id' => new OrganizationsResource($this->organization),
            'city' => implode(',',$this->cities()->pluck('name')->toArray()),
            'role_id' => new UserTypes($this->role),
            'provider' => $this->provider,
            'avatar' => $this->avatar ? '/users/avatars/'.$this->avatar : null,
            'quiz' => UserQuizResource::collection($this->quiz_answers),
            'is_before_school' => $this->role_id == Role::USER_BEFORE_SCHOOL || $this->role_id == Role::USER_BEFORE_SCHOOL_SECOND ? true : false,
            'is_midrashot' => $this->role_id == Role::USER_BEFORE_SCHOOL_SECOND ? true : false,
            'is_hr' => $this->role_id == Role::HR ? true : false,
            'auth_start' => $this->auth_start,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d\TH:i'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d\TH:i'),
            'school' => $this->school_id ? School::find($this->school_id) : null,

        ];
    }
}
