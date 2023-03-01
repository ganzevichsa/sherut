<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    public function index()
    {
        $addresses = Address::all();
        return view('addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:addresses,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $address = new Address();
        $address->name = $data['name'];
        $address->save();
        return response()->json($address);
    }

    public function show($id)
    {
        return response()->json(Address::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:addresses,name,'.$id,
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $address = Address::find($id);
        if(!$address) {
            abort(404);
        }
        $address->name = $data['name'];
        $address->save();
        return response()->json($address);
    }

    public function destroy($id)
    {
        $address = Address::find($id);
        if(!$address) {
            abort(404);
        }
        $address->delete();
        return redirect()->back()->with('message','Address successfully removed');
    }
}
