<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserJob extends Model
{
    const APPLY = 0;
    const APPROVED = 1;
    const CANCEL = 2;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }

}
