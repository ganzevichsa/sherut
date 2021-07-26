<?php

namespace App\Http\Controllers;

use App\Job;
use App\Subcategory;
use Illuminate\Http\Request;

class SubcategoriesController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $subcategories = Subcategory::where('category_id',$data['id'])->get();
        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:subcategories,name',
            'category_id' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $subcategory = new Subcategory();
        $subcategory->category_id = $data['category_id'];
        $subcategory->name = $data['name'];
        $subcategory->priority = $data['priority'];
        $subcategory->value = $data['value'];
        $subcategory->text = $data['text'];
        $subcategory->save();
        return response()->json($subcategory);
    }

    public function show($id)
    {
        return response()->json(Subcategory::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required,name,'.$id,
            'category_id' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $subcategory = Subcategory::find($id);
        if(!$subcategory) {
            abort(404);
        }
        $subcategory->category_id = $data['category_id'];
        $subcategory->name = $data['name'];
        // $subcategory->priority = $data['priority'];
        $subcategory->value = $data['value'];
        $subcategory->text = $data['text'];
        $subcategory->save();
        return response()->json($subcategory);
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);
        if(!$subcategory) {
            abort(404);
        }
        Job::where('subcategory_id',$id)->update(['subcategory_id' => null]);
        $subcategory->delete();
        return redirect()->back()->with('message','Subcategory successfully removed');
    }
}
