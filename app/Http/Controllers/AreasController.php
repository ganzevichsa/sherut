<?php

namespace App\Http\Controllers;

use App\Area;
use App\City;
use Illuminate\Http\Request;

class AreasController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        $cities = City::all();
        return view('areas.index',compact('areas', 'cities'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:areas,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $category = new Area();
        $category->name = $data['name'];
        $category->save();
        return response()->json($category);
    }

    public function show($id)
    {
        return response()->json(Area::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:areas,name,'.$id,
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $area = Area::find($id);
        if(!$area) {
            abort(404);
        }
        $area->name = $data['name'];
        $area->save();
        return response()->json($area);
    }

    public function destroy($id)
    {
        $area = Area::find($id);
        if(!$area) {
            abort(404);
        }
        City::where('area_id',$id)->update(['area_id' => null]);
        $area->delete();
        return redirect()->back()->with('message','Area successfully removed');
    }
}
