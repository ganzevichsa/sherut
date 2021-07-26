<?php

namespace App\Http\Controllers;

use App\StageOfEducation;
use Illuminate\Http\Request;

class StageOfEducationsController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:stage_of_education,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $education = new StageOfEducation();
        $education->name = $data['name'];
        $education->save();
        return response()->json($education);
    }
}
