<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN = 1;
    const HR = 2;
    const USER_BEFORE_SCHOOL = 3;
    const USER_BEFORE_SCHOOL_SECOND = 4;
    const USER_AFTER_SCHOOL = 5;
    const USER_AFTER_SCHOOL_SECOND = 6;

    public function posts()
    {
        return $this->belongsToMany('App\Post','post_roles','role_id');
    }

    public function blogs()
    {
        return $this->belongsToMany('App\Blog','blog_roles','role_id');
    }
}
