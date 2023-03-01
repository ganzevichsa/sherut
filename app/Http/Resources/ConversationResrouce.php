<?php

namespace App\Http\Resources;

use App\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResrouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth('web')->user();
        if ($user->role_id != Role::HR) {
            $otherUser = $this->hr;
        } else {
            $otherUser = $this->user;
        }
        return [
            'first_name' => $otherUser->first_name,
            'last_name' => $otherUser->last_name,
            'organization_name' => $otherUser->organization ? $otherUser->organization->name : '',
            'avatar' => $otherUser->avatar ? '/users/avatars/' . $otherUser->avatar : '',
            'new_message' => $this->messages()->where('user_id','!=', $user->id)->where('is_read',0)->count()
        ];
    }
}
