<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    // protected $table = 'job_jobs';
    // protected $fillable = ['out_identificator', 'site', 'url', 'title', 'status', 'target_audience', 'main_areas_of_study', 'route_midrasha', 'views', 'organization_id', 'manager_id', 'job_type_id', 'home', 'out', 'dormitory', 'about', 'description', 'category_id', 'subcategory_id', 'city_id', 'is_admin_update', 'location_id', 'address_id', 'stage_of_education_id', 'other_hr_name', 'other_hr_phone', 'how_to_sort', 'nucleus', 'video_url', 'doc_urls', 'active', 'checked', 'year', 'job_for', 'last_date_for_registration', 'count_of_all_positions', 'stars', 'program', 'deleted_at', 'midrasha_route'];

    public function users() {
        return $this->belongsToMany('App\User','user_jobs');
    }

    public function images() {
        return $this->hasMany('App\JobImage');
    }

    public function hr() {
        return $this->belongsToMany('App\User','job_hrs');
    }

    public function type_of_year() {
        return $this->belongsToMany('App\TypeOfYear','job_type_of_years');
    }

    public function organizationRoute() {
        return $this->belongsToMany('App\OrganizationRoute','organization_routes_jobs');
    }

    public function city() {
        return $this->belongsTo('App\City');
    }

    public function type() {
        return $this->belongsTo('App\JobType','job_type_id');
    }

    public function category() {
        return $this->belongsTo('App\Category');
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory');
    }


    public function organization() {
        return $this->belongsTo('App\Organization');
    }

    public function midrasha() {
        return $this->hasOne('App\JobMidrashaInfo');
    }

    public function reviews() {
        return $this->hasMany('App\JobReview');
    }

    public function jobUsers() {
        return $this->hasMany('App\UserJob');
    }
}
