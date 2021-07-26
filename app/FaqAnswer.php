<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaqAnswer extends Model
{
    const WAITING = 0;
    const ACCEPTED = 1;
}
