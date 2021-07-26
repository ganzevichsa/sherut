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
use App\Organization;
use App\Role;
use App\UserJob;
use App\Year;
use Illuminate\Http\Request;

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
        $data = $request->all();
        $type = null;

        if($this->user->role_id == Role::USER_BEFORE_SCHOOL_SECOND) {
            $type = JobType::MIDRASHA;
        } else {
            $type = JobType::NATIONAL_SERVICE;
        }

        $jobs = new Job();
        $jobs = $jobs->leftJoin('cities', 'jobs.city_id', '=', 'cities.area_id');

        if ($this->user->role_id == Role::HR) {
            $jobs = $this->user->jobs();
        }

        if($type) {
            $jobs = $jobs->where('job_type_id',$type);
        }

        if(isset($data['search']) && !empty($data['search'])) {
            $jobs = $jobs->where('title', 'LIKE', '%'.$data['search'].'%');
        } else {
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

            if (isset($data['subcategories'])) {
                $jobs = $jobs->whereIn('jobs.subcategory_id', $data['subcategories']);
            } else {
                if (isset($data['categories'])) {
                    $jobs = $jobs->whereIn('jobs.category_id', $data['categories']);
                }
            }

            if (isset($data['organizations'])) {
                $jobs = $jobs->whereIn('jobs.organization_id', $data['organizations']);
            }
            if (isset($data['areas'])) {
                // $cities = City::where('area_id', $data['areas'])->pluck('id');
                // $jobs = $jobs->whereIn('city_id', $cities);
                $jobs = $jobs->where('cities.area_id', $data['areas']);
            }
            if (isset($data['nucleus'])) {
                $jobs = $jobs->where('jobs.nucleus', $data['nucleus']);
            }
            if (isset($data['is_home'])) {
                $jobs = $jobs->where('jobs.home', '>', 0);
            }
            if (isset($data['is_out'])) {
                $jobs = $jobs->where('jobs.out', '>', 0);
            }
            if (isset($data['is_dormitory'])) {
                $jobs = $jobs->where('jobs.dormitory', '>', 0);
            }
            // FILTER END
        }
        if ($this->user->role_id != Role::HR) {
            $jobs = $jobs->where('jobs.count_of_all_positions', '>', 0);
        }

        $jobs = $jobs->select('jobs.*');

        if ($sort == 'date') {
            $jobs = $jobs->orderBy('jobs.created_at', 'desc')->skip($skip)->limit($this->limit)->get();
        } elseif ($sort == 'stars') {
            $jobs = $jobs->orderBy('jobs.stars', 'desc')->skip($skip)->limit($this->limit)->get();
        }

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
        if($this->user->role_id == Role::USER_BEFORE_SCHOOL_SECOND) {
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

        if (!JobView::where('job_id', $job->id)->where('user_id', $this->user->id)->first()) {
            $view = new JobView();
            $view->job_id = $job->id;
            $view->user_id = $this->user->id;
            $view->save();
            $job->views += 1;
            $job->save();
        }

        return new SingleJobResource($job);
    }

    public function apply($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        if($this->user->opportunities()->where('job_id',$job->id)->first()) {
            return response()->json(['message' => 'Something went wrong...'], 400);
        }
        $opportunity = new UserJob();
        $opportunity->user_id = $this->user->id;
        $opportunity->job_id = $job->id;
        $opportunity->save();
        return response()->json(['message' => ''],200); //  TODO add message
    }




}
