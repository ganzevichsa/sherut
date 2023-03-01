<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserQuiz extends Model
{
    public function quiz()
    {
        return $this->belongsTo('App\Quiz');
    }

    public function quiz_answer()
    {
        return $this->belongsTo('App\QuizAnswer');
    }
}
