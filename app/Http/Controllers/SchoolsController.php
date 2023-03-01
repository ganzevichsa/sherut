<?php

namespace App\Http\Controllers;

use App\School;
use Illuminate\Http\Request;

class SchoolsController extends Controller
{
    public function index()
    {
        $schools = School::all();
        return view('schools.index',compact('schools' ));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:schools,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $category = new School();
        $category->name = $data['name'];
        $category->save();
        return response()->json($category);
    }

    public function show($id)
    {
        return response()->json(School::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:schools,name,'.$id,
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $area = School::find($id);
        if(!$area) {
            abort(404);
        }
        $area->name = $data['name'];
        $area->save();
        return response()->json($area);
    }

    public function destroy($id)
    {
        $area = School::find($id);
        if(!$area) {
            abort(404);
        }
        $area->delete();
        return redirect()->back()->with('message','School successfully removed');
    }
}
