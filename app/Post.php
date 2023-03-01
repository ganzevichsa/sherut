<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    
    public function favorites()
    {
        return $this->belongsToMany('App\User', 'favorite_posts', 'post_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'post_roles', 'post_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function subcategory()
    {
        return $this->belongsTo('App\Subcategory');
    }
}
