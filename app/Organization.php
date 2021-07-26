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
}
