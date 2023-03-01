<?php

namespace App\Http\Controllers;

use App\Area;
use App\Category;
use App\City;
use App\Http\Resources\JobsResource;
use App\Http\Resources\SimpleTableResource;
use App\Http\Resources\SingleJobResource;
use App\Job;
use App\JobType;
use App\JobView;
use App\JobFavorite;
use App\Organization;
use App\Role;
use App\UserJob;
use App\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthJobsController extends Controller
{
    protected $user;
    protected $limit = 20;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function getJobs(Request $request, $skip = 0, $sort = 'date')
    {
        // die('959');
        $data = $request->all();
        $type = null;
        Log::info('data');
        Log::info($data);
        Log::info('skip');
        Log::info($skip);
        Log::info('sort');
        Log::info($sort);

        if ($this->user->role_id == Role::USER_BEFORE_SCHOOL_SECOND) {
            $type = JobType::MIDRASHA;

        } else {
            $type = JobType::NATIONAL_SERVICE;

        }
        $jobs = new Job();
        // $jobs = $jobs->leftJoin('cities', 'jobs.city_id', '=', 'cities.area_id');
        if ($this->user->role_id == Role::HR) {
            $jobs = $this->user->jobs();
        }

        if ($type) {
            $jobs = $jobs->where('job_type_id', $type);


        }


            // FILTER START
            // if (isset($data['years'])) {
            //     $year = Year::find($data['years']);
            //     if ($year) {
            //         $yearData = date('Y');
            //         if ($year->key == 'next_year') {
            //             $yearData = date('Y', strtotime(date("Y-m-d", time()) . " + 365 day"));
            //         }
            //         $jobs = $jobs->where('year', $yearData);
            //     }
            // }
            if (isset($data['search']) && !empty($data['search'])) {
                $jobs = $jobs->where('title', 'LIKE', '%' . $data['search'] . '%');
            } 

            if (isset($data['subcategories'])) {
                $jobs = $jobs->where('subcategory_id', $data['subcategories']);
            } 
   
            
            if (isset($data['categories'])) {
                $jobs = $jobs->where('category_id', $data['categories']);
            }

            if (isset($data['organizations'])) {
                $jobs = $jobs->where('organization_id', $data['organizations']);
            }

            
            if (isset($data['job_for'])) {
                $jobs = $jobs->where('job_for', $data['job_for']);
            }

            if (isset($data['areas'])) {
                $areas = Area::where('name', $data['areas'])->first();
                if(isset($areas)){
                    $cities = City::where('area_id', $areas->id)->pluck('id');
                    $jobs = $jobs->whereIn('city_id', $cities->toArray());
                }
                else{
                    $jobs = [];
                    return JobsResource::collection($jobs);
                }
            }
            if (isset($data['nucleus'])) {
                $jobs = $jobs->where('nucleus', $data['nucleus']);
            }
            if (isset($data['audience'])) {
                $jobs = $jobs->where('target_audience', $data['audience']);
            }
            if (isset($data['places'])) {
                $jobs = $jobs->where('place', $data['places']);
            }
            if (isset($data['program'])) {
                $jobs = $jobs->where('program', $data['program']);
            }
      
            if (isset($data['years'])) {
                $jobs = $jobs->where('year', $data['years']);
            }
            
            if (isset($data['is_home'])) {
                $jobs = $jobs->where('home', '>', 0);
            }
            if (isset($data['is_out'])) {
                $jobs = $jobs->where('out', '>', 0);
            }
            if (isset($data['is_dormitory'])) {
                $jobs = $jobs->where('dormitory', '>', 0);
            }
            // FILTER END

        if ($this->user->role_id != Role::HR) {
            $jobs = $jobs->where('count_of_all_positions', '>', 0);
   
        }
        if (isset($data['sort_order'])) {
            $sort_order = $data['sort_order'];
        }
        else{
            $sort_order = 'desc';
        }

        // $jobs = $jobs->select('jobs.*');

        if ($sort == 'date') {
            $jobs = $jobs->orderBy('created_at', $sort_order)->skip($skip)->limit($this->limit);
        } elseif ($sort == 'stars') {
            $jobs = $jobs->orderBy('stars', $sort_order)->skip($skip)->limit($this->limit);
        } elseif ($sort == 'title') {
            $jobs = $jobs->orderBy('title', $sort_order)->skip($skip)->limit($this->limit);
        }

        $jobs = $jobs->get();

        return JobsResource::collection($jobs);
    }

    public function addFavorite($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Not found this job'], 404);
        }
        if ($this->user->favorites()->where('job_id', $id)->first()) {
            $this->user->favorites()->detach($id);
        } else {
            $this->user->favorites()->attach($id);
        }
        return response()->json(['message' => true], 200);
    }

    public function filterGetData()
    {
        $years = SimpleTableResource::collection(Year::all());
        $areas = SimpleTableResource::collection(Area::all());
        $places = [
            [
                'id' => 'home',
                'name' => 'תקן בית'
            ],
            [
                'id' => 'out',
                'name' => 'תקן דירה'
            ],
            [
                'id' => 'dormitory',
                'name' => 'פנימיה'
            ],
        ];
        $job_for_list = [
            'מיועד לבנים בלבד',
            'מיועד לבנות בלבד',
            'מיועד לשני המינים'
        ];
        if ($this->user->role_id == Role::USER_BEFORE_SCHOOL_SECOND) {
            $howToSort = [
                'מיונים מוקדמים',
                'שאלון העדפות',
                'סיירות רגילות'
            ];
            $program = [
                'תכנית אלול',
                'תכנית מלאה',
                'מדרשת שילוב',
            ];
            return response()->json([
                'years' => $years,
                'how_to_sort' => $howToSort,
                'program' => $program,
                'job_for' => $job_for_list,
                'areas' => $areas,
                'places' => $places
            ]);
        }
        $categories = SimpleTableResource::collection(Category::all());
        $organizations = SimpleTableResource::collection(Organization::all());
        return response()->json([
            'years' => $years,
            'categories' => $categories,
            'organizations' => $organizations,
            'areas' => $areas,
            'nucleus' => [
                'כן',
                'לא'
            ],
            'places' => $places,
            'job_for' => $job_for_list,
        ]);
    }

    public function view($id)
    {
        $job = Job::find($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        // $job['count_job_users'] = $job->jobUsers->count();
        if (!JobView::where('job_id', $job->id)->where('user_id', $this->user->id)->first()) {
            $view = new JobView();
            $view->job_id = $job->id;
            $view->user_id = $this->user->id;
            $view->save();
            $job->views += 1;
            $job->save();
        }

        $get_fav = JobFavorite::where([
            'user_id' => $this->user->id,
            'job_id' => $job->id
        ])->first();
   
        if (!empty($get_fav)) {
            $job['is_favorite'] = 1;
        } else {
            $job['is_favorite'] = 0;
        }

        return new SingleJobResource($job);
    }

    public function viewEdit($id)
    {
        $job = Job::find($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }


        return new ViewEditJobMidrashaResource($job);
    }

    public function apply($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        $user_job = $this->user->opportunities()->where('job_id', $job->id)->first();
        if ($user_job) {

            $user_job->touch();
            return response()->json(['message' => 'Update'], 200);
        }
        $opportunity = new UserJob();
        $opportunity->user_id = $this->user->id;
        $opportunity->job_id = $job->id;
        $opportunity->save();

        // $count_of_taken_positions = $job->count_of_taken_positions;

        // $job->count_of_taken_positions = $count_of_taken_positions + 1;

        return response()->json(['message' => ''], 200); //  TODO add message
    }


}
