<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function subcategories()
    {
        return $this->hasMany('App\Subcategory');
    }

    public function images()
    {
        return $this->hasMany('App\CategoryImage');
    }

    public function uploadImage($avatar)
    {
        $image = $avatar;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path('app/public/categories');
        $image->move($destinationPath, $name);
        return $name;
    }
}
