<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:locations,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $location = new Location();
        $location->name = $data['name'];
        $location->save();
        return response()->json($location);
    }

    public function show($id)
    {
        return response()->json(Location::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:locations,name,' . $id,
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $location = Location::find($id);
        if (!$location) {
            abort(404);
        }
        $location->name = $data['name'];
        $location->save();
        return response()->json($location);
    }

    public function destroy($id)
    {
        $location = Location::find($id);
        if(!$location) {
            abort(404);
        }
        $location->delete();
        return redirect()->back()->with('message','Location successfully removed');
    }
}
