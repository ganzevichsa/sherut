<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'organization_id', 'phone','enabled'
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jobs() {
        return $this->belongsToMany('App\Job', 'job_hrs');
    }

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }

    public function provider()
    {
        return $this->belongsTo('App\AuthProvider');
    }

    public function quiz_answers()
    {
        return $this->hasMany('App\UserQuiz');
    }

    public function opportunities()
    {
        return $this->hasMany('App\UserJob');
    }

    public function favorites()
    {
        return $this->belongsToMany('App\Job','job_favorites','user_id');
    }

    public function favorite_posts()
    {
        return $this->belongsToMany('App\Post','favorite_posts','user_id');
    }

    public function schools()
    {
        return $this->belongsToMany('App\School','user_schools','user_id');
    }

    public function cities()
    {
        return $this->belongsToMany('App\City','user_cities','user_id');
    }

    public function uploadAvatar($avatar)
    {
        $image = $avatar;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path('app/public/users/avatars');
        $image->move($destinationPath, $name);
        return $name;
    }

    public function areas() {
        return $this->belongsToMany('App\Area','hr_areas');
    }
}
