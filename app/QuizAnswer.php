<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'answer'];
}
