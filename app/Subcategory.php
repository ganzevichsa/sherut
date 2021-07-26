<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function images()
    {
        return $this->hasMany('App\SubcategoryImage');
    }

    public function uploadImage($avatar)
    {
        $image = $avatar;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path('app/public/subcategories');
        $image->move($destinationPath, $name);
        return $name;
    }
}
