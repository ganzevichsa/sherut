<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryImage;
use App\Http\Resources\SingleCategoryResource;
use App\Job;
use App\Subcategory;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        return view('categories.index',compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $category = new Category();
        $this->_main_control_block($category, $data);
        return redirect()->back()->with('message', 'Category successfully created');
    }

    public function show($id)
    {
        return new SingleCategoryResource(Category::find($id));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:categories,name,'.$id,
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $category = Category::find($id);
        if(!$category) {
            abort(404);
        }
        $this->_main_control_block($category, $data);
        return redirect()->back()->with('message', 'Category successfully updated');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category) {
            abort(404);
        }
        Job::where('category_id',$id)->update(['category_id' => null]);
        Subcategory::where('category_id',$id)->update(['category_id' => null]);
        $category->delete();
        return redirect()->back()->with('message','Category successfully removed');
    }

    public function removeFile($id)
    {
        $categoryImage = CategoryImage::find($id);
        if(!$categoryImage) {
            return response()->json(['message' => 'not_found'],404);
        }
        $categoryImage->delete();
        return response()->json(['message' => 'success'],200);
    }

    private function _main_control_block($category, $data)
    {
        $category->name = $data['name'];
        $category->video_url = $data['video_url'];
        $category->text = $data['text'];
        if(isset($data['value']))
            $category->value = $data['value'];
        if(isset($data['priority']))
            $category->priority = $data['priority'];
        if(isset($data['min_value']))
            $category->min_value = $data['min_value'];
        $category->save();
        if (!empty($data['images'])) {
            foreach ($category->images as $image) {
                if (file_exists('/storage/categories.' . $image)) {
                    unlink(storage_path('app/public/categories/' . $image));
                }
            }
            $category->images()->delete();
            foreach ($data['images'] as $image) {
                $categoryImage = new CategoryImage();
                $image = $category->uploadImage($image);
                $categoryImage->category_id = $category->id;
                $categoryImage->image = $image;
                $categoryImage->save();
            }
        }
    }
}
