<?php

namespace App\Http\Controllers;

use App\Area;
use App\Category;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\SimpleTableResource;
use App\Http\Resources\SingleCategoryResource;
use App\Http\Resources\SubcategoryResource;
use App\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LibrariesController extends Controller
{
    public function getSubcategory($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(SubcategoryResource::collection($category->subcategories));
    }

    public function getCities($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['message' => 'Area not found'], 404);
        }
        return response()->json(SimpleTableResource::collection($area->cities));
    }

    public function getDurationByYear($year)
    {
        $durations = [
            1 => 'שנה אחת',
            2 => 'שנתיים',
            3 => 'שלוש שנים'
        ];
        if (Carbon::now()->format("Y") - $year >= count($durations)) {
            return response()->json($durations);
        }
        krsort($durations);
        $i = 1;
        foreach ($durations as $key => $duration) {
            if ($i > Carbon::now()->format("Y") - $year) {
                unset($durations[$key]);
            }
            $i++;
        }
        asort($durations);
        return response()->json($durations);
    }

    public function getCategories()
    {
        return CategoriesResource::collection(Category::where('is_main', 1)->get());
    }

    public function getAreas()
    {
        $areas = Area::all();
        return response()->json(SimpleTableResource::collection($areas));
    }

    public function getCategory($id)
    {
        if (!$category = Category::find($id)) {
            return response()->json(['message' => 'not_found'], 400);
        }
        return new SingleCategoryResource($category);
    }

    public function getSubCategoryByID($id)
    {
        if (!$subcategory = Subcategory::find($id)) {
            return response()->json(['message' => 'not_found'], 400);
        }
        return new SubcategoryResource($subcategory);
    }
}
