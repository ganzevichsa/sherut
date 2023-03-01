<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    const WAITING = 0;
    const ACCEPTED = 1;

    protected $fillable = [
        'user_id',
        'job_id',
        'hr_id',
        'question',
        'status'
    ];

    public function answers()
    {
        return $this->hasMany('App\FaqAnswer');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function hr()
    {
        return $this->belongsTo('App\User');
    }
}
