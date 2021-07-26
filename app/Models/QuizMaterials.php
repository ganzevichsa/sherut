<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizMaterials extends Model
{
    protected $fillable = ['title', 'cat', 'value', 'point'];
}
