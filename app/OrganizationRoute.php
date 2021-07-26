<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationRoute extends Model
{
    public function organization() {
        return $this->belongsTo('App\Organization');
    }
}
