<?php

namespace App\Http\Controllers;

use App\Job;
use App\JobType;
use App\Organization;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use App\Exports\JobsExport;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $organizations = Organization::all();
        $hrs = User::where('role_id',Role::HR)->get();
        $jobs = Job::orderBy('created_at','DESC')->paginate(20);
        $jobTypes = JobType::all();
        return view('home',compact('jobs','organizations','hrs', 'jobTypes'));
    }

    public function jobsList(Request $request)
    {
        $data = $request->all();
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Job::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Job::select('count(*) as allcount')->where('title', 'like', '%' .$searchValue . '%')->count();
        // Fetch records
        if($columnName == 'category') {
            $records = DB::table('jobs')
                ->join('categories', 'categories.id', '=', 'jobs.category_id')
                ->orderBy('categories.name',$columnSortOrder);
        } elseif($columnName == 'city') {
            $records = DB::table('jobs')
                ->join('cities', 'cities.id', '=', 'jobs.city_id')
                ->orderBy('cities.name',$columnSortOrder);
        } elseif($columnName == 'subcategory') {
            $records = DB::table('jobs')
                ->join('subcategories', 'subcategories.id', '=', 'jobs.subcategory_id')
                ->orderBy('subcategories.name',$columnSortOrder);
        }else{
            $records = Job::orderBy($columnName,$columnSortOrder)
                ->where('jobs.title', 'like', '%' .$searchValue . '%');


        }
        if(isset($data['hr'])) {
            $records = $records->whereHas('hr', function ($query) use ($data) {
                $query->where('id',$data['hr']);
            });
        }
        if(isset($data['org'])) {
            $records = $records->whereHas('hr', function ($query) use ($data) {
                $query->where('organization_id',$data['org']);
            });
        }
        if(isset($data['job_type_id'])) {
            $records = $records->where('job_type_id',$data['job_type_id']);
        }
        if(isset($data['year'])) {
            $records = $records->where('last_date_for_registration','<',(new Carbon($data['year']."-12-31")));
        }
        $records = $records->select('jobs.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data = [];
        $i = 0;
        foreach($records as $job) {
            $job = Job::find($job->id);
            $data[$i]['is_admin_update'] = $job->is_admin_update ? 'כן' : 'לא';
            $data[$i]['type'] = $job->type ? $job->type->name : '';
            $data[$i]['job_url'] = '<a href="'.$job->url.'" target="_blank">'.$job->url.'</a>';
            $data[$i]['hr'] = $job->hr ? implode(' ',$job->hr()->pluck('phone')->toArray()) : '';
            $data[$i]['title'] = $job->title;
            $data[$i]['home'] = $job->home;
            $data[$i]['out'] = $job->out;
            $data[$i]['dormitory'] = $job->dormitory;
            $data[$i]['organization'] = $job->organization ? $job->organization->name : '';
            $data[$i]['category'] = $job->category ? $job->category->name : '';
            $data[$i]['subcategory'] = $job->subcategory ? $job->subcategory->name : '';
            $data[$i]['area'] = $job->city ? ($job->city->area ? $job->city->area->name : '') : '';
            $data[$i]['nucleus'] = $job->nucleus;
            $data[$i]['city'] = $job->city ? $job->city->name : '';
            $data[$i]['created_at'] = (new Carbon($job->created_at))->addHours(3);
            if(!$job->checked) {
                $data[$i]['checked'] = '<input data-url="'.route('jobs.checked',$job->id).'" type="checkbox" onclick="jobChecked(this)">';
            }else{
                $data[$i]['checked'] = '<input data-url="'.route('jobs.checked',$job->id).'" checked type="checkbox" onclick="jobChecked(this)">';
            }
            $data[$i]['actions'] = ' <form method="POST" action="'.route('jobs.destroy',$job->id).'">
                                        <input type="hidden" name="_token" value="'.csrf_token().'">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <a href="'.route('jobs.edit',$job->id).'" class="btn btn-gray">לַעֲרוֹך</a>
                                        <button class="btn btn-danger">לִמְחוֹק</button>
                                    </form>';
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data
        );
        return response()->json($response);
    }

    public function downloadExcel ()
    {
        $export = new JobsExport();

        $download = Excel::download($export, 'jobs-'.time().'.xlsx');
        return $download;
    }

}
