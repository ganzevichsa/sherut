<?php

namespace App\Http\Controllers;

use App\City;
use App\Job;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $cities = City::where('area_id',$data['id'])->get();
        return response()->json($cities);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:cities,name',
            'area_id' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $city = new City();
        $city->area_id = $data['area_id'];
        $city->name = $data['name'];
        $city->save();
        return response()->json($city);
    }

    public function show($id)
    {
        return response()->json(City::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:cities,name,'.$id,
            'area_id' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $city = City::find($id);
        if(!$city) {
            abort(404);
        }
        $city->area_id = $data['area_id'];
        $city->name = $data['name'];
        $city->save();
        return response()->json($city);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if(!$city) {
            abort(404);
        }
        Job::where('city_id',$id)->update(['city_id' => null]);
        $city->delete();
        return redirect()->back()->with('message','City successfully removed');
    }
}
