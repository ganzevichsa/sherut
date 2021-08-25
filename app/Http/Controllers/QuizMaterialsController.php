<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizMaterials;
use App\Category;
use App\Subcategory;

class QuizMaterialsController extends Controller
{
    public function index ()
    {
    	$materials = QuizMaterials::get();
    	return view('quiz-materials.index', compact('materials'));
    }

    public function create (Request $request)
    {
    	if($request->method() == "POST"){

    		try {
	    		QuizMaterials::create([
	    			'title' => $request->title,
                    'cat' => $request->cat
	    		]);

	    		return redirect()->route('quiz.materials');
	    	} catch (\Exception $ex) {
	    		// dd($ex);
	    		return redirect()->route('quiz.materials')->with('message', 'Error add Quiz Material');
	    	}


    	}

        $categories = Category::all();
        $subcategories = Subcategory::all();
    	return view('quiz-materials.edit-add', compact('categories', 'subcategories'));
    }

    public function edit (Request $request, QuizMaterials $material)
    {
    	if($request->method() == "POST"){
    		$material->update(['title' => $request->title, 'cat' => $request->cat]);
    		return redirect()->route('quiz.materials')->with('message', 'Update success');
    	}

        $categories = Category::all();
        $subcategories = Subcategory::all();
    	return view('quiz-materials.edit-add', compact('material', 'categories', 'subcategories'));
    }

    public function delete (QuizMaterials $material)
    {
        $material->delete();
        return redirect()->back();
    }
}
