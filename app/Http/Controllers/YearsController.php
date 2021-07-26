<?php

namespace App\Http\Controllers;

use App\Year;
use Illuminate\Http\Request;

class YearsController extends Controller
{
    public function index()
    {
        $years = Year::all();
        return view('years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this_year = Year::where('key','this_year')->first();
        $this_year->name = $data['this_year'];
        $this_year->save();
        $next_year = Year::where('key','next_year')->first();
        $next_year->name = $data['next_year'];
        $next_year->save();
        return redirect(route('years.index'))->with('message', 'Years updated successfully');
    }
}
