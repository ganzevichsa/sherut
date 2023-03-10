<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Http\Resources\SmallJobResource;
use App\Http\Resources\SmallOrganizationResource;
use App\Job;
use App\Organization;
use App\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebController extends Controller
{
    public function getData()
    {
        $organizations = SmallOrganizationResource::collection(Organization::all());
        $jobs = SmallJobResource::collection(Job::all());
        $years = Year::all();
        return response()->json([
            'organizations' => $organizations,
            'jobs' => JobResource::collection($jobs),
            'years' => $years
        ]);
    }

    public function validatePhone(Request $request)
    {
        $rules['phone'] = 'required|string|unique:users';
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        return response()->json(['phone is free'],200);

    }

    public function getToken()
    {
        $token = auth()->getToken();

        return response()->json(['token' => $token],200);
    }
}
