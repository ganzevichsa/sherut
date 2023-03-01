<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public function managers()
    {
        return $this->hasMany('App\OrganizationManager');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function images()
    {
        return $this->hasMany('App\OrganizationImage');
    }

    public function uploadImage($avatar)
    {
        $image = $avatar;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path('app/public/organizations/images');
        $image->move($destinationPath, $name);
        return $name;
    }
}
