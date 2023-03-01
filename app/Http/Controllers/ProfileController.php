<?php

namespace App\Http\Controllers;

use App\City;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobsResource;
use App\Http\Resources\ProfileRequestsResource;
use App\Http\Resources\SimpleTableResource;
use App\Http\Resources\UsersResource;
use App\Http\Resources\UserTypes;
use App\Role;
use App\JobFavorite;
use App\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function requests()
    {
        return ProfileRequestsResource::collection($this->user->opportunities);
    }

    public function favorites()
    {

        return JobsResource::collection($this->user->favorites);
    }

    public function favoriteDelete($id)
    {

        $get_fav = JobFavorite::where([
            'user_id' => $this->user->id,
            'job_id' => $id
        ])->first();
       
        if (!empty($get_fav)) {
            $get_fav->delete();
            // 
        }
        else{
            $get_fav = new JobFavorite;
            $get_fav->user_id = $this->user->id;
            $get_fav->job_id = $id;
            $get_fav->save();
        }
        
        return JobsResource::collection($this->user->favorites);
    }

    public function getInfo()
    {
        $schools = SimpleTableResource::collection(School::all());
        $userTypes = UserTypes::collection(Role::where('is_simple_user', true)->get());
        return response()->json([
            'schools' => $schools,
            'types' => $userTypes,
            'dateTypes' => [
                [
                    'name' => 'תאריך לידה לועזי',
                    'is_regular' => true
                ],
                [
                    'name' => 'תאריך לידה עברי',
                    'is_regular' => false
                ],
            ],
            'cities' => SimpleTableResource::collection(City::all())
        ]);
    }

    public function storeSchool(Request $request)
    {
        $rules = [
            'name' => 'required|unique:schools,name',
            'email' => 'required|unique:users,email,' . $this->user->id,
            'type_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        $school = new School();
        $school->name = $data['name'];
        $school->save();
        $data['school_id'] = $school->id;
        $this->_storeAdditionalInfo($data);
        return new UsersResource($this->user);
    }

    public function storeAdditionalInfo(Request $request)
    {
        $rules = [
            'email' => 'required_without_all:type_id,school_id|unique:users,email,' . $this->user->id,
            'type_id' => 'required_without_all:email,school_id',
            'school_id' => 'required_without_all:email,type_id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        $this->_storeAdditionalInfo($data);
        return new UsersResource($this->user);
    }

    public function storeBirthday(Request $request)
    {
        $rules = [
            'is_regular' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        if($data['is_regular']) {
            $rules['year'] = 'required|numeric';
            $rules['day'] = 'required|numeric';
            $rules['month'] = 'required|numeric';
        } else {
            $rules['year'] = 'required';
            $rules['day'] = 'required';
            $rules['month'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if($data['is_regular']) {
            $this->user->birthdate = $data['year'] ."-". $data['month'] ."-". $data['day'];
            $this->user->birthdate_hebrew = '';
        } else {
            $this->user->birthdate_hebrew = $data['year'] ."-". $data['month'] ."-". $data['day'];
            $this->user->birthdate = '';
        }
        $this->user->save();
        return new UsersResource($this->user);
    }

    public function storeCity(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'name' => 'required|unique:cities,name',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        $city = new City();
        $city->name = $data['name'];
        $city->save();
        $data['city_id'] = $city->id;
        $this->_storeDetailsInfo($data);
        return new UsersResource($this->user);
    }

    public function storeDetails(Request $request)
    {
        $rules = [
            'first_name' => 'required_without_all:last_name,city_id',
            'last_name' => 'required_without_all:first_name,city_id',
            'city_id' => 'required_without_all:first_name,last_name',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        $this->_storeDetailsInfo($data);
        return new UsersResource($this->user);
    }

    public function _storeAdditionalInfo($data)
    {
        $this->user->role_id = $data['type_id'];
        if (isset($data['school_id']) && !empty($data['school_id'])) {
            $this->user->school_id = $data['school_id'];
        }
        $this->user->email = $data['email'];
        if($this->user->role_id == 2){
            if($this->user->id != 1382){
                $this->user->auth_start = 0;
            }
        }
        $this->user->save();
    }

    public function _storeDetailsInfo($data)
    {
        if(isset($data['first_name'])){
            $this->user->first_name = $data['first_name'];
        }
        if(isset($data['last_name'])){
            $this->user->last_name = $data['last_name'];
        }
        if (isset($data['city_id']) && !empty($data['city_id'])) {
            $this->user->cities()->sync([$data['city_id']]);
        }
        if (isset($data['avatar']) && !empty($data['avatar'])) {
            if ($this->user->avatar) {
                if (File::exists('storage/users/avatars/' . $this->user->avatar)) {
                    unlink(storage_path('app/public/users/avatars/' . $this->user->avatar));
                }
            }
            $this->user->avatar = $this->user->uploadAvatar($data['avatar']);
        }
        
        if($this->user->role_id == 2){
            if($this->user->id != 1382){
                $this->user->auth_start = 0;
            }
        }

        $this->user->save();
    }
}
