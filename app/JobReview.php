<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobReview extends Model
{
    const ACTIVE = 1;

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
