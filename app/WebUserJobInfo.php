<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebUserJobInfo extends Model
{
    public function user()
    {
        $this->belongsTo('App\User');
    }

    public function job()
    {
        $this->belongsTo('App\Job');
    }

    public function year()
    {
        $this->belongsTo('App\Year');
    }
}
