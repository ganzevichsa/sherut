<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['question', 'answer'];

    const TYPES = [
        'simple' => 0, // Is have value - false
        'scale' => 1 // Is have value - true
    ];
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function answers()
    {
        return $this->hasMany('App\QuizAnswer');
    }
}
