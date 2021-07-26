<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeOfYear extends Model
{
    public function job() {
        return $this->belongsToMany('App\Job','job_type_of_years');
    }
}
