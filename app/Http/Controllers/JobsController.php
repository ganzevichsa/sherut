<?php

namespace App\Http\Controllers;

use App\Address;
use App\Area;
use App\Category;
use App\City;
use App\Http\Resources\JobResource;
use App\Job;
use App\JobImage;
use App\JobMidrashaInfo;
use App\JobType;
use App\Location;
use App\Organization;
use App\OrganizationRoute;
use App\StageOfEducation;
use App\TypeOfYear;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    protected $exist_admin_change = false;
    protected $howToSort = [
    'מיונים מוקדמים',
    'שאלון העדפות',
    'סיירות רגילות'
    ];
    protected $nucleus = [
    'כן',
    'לא'
    ];
    protected $job_for_list = [
        'מיועד לבנים בלבד',
        'מיועד לבנות בלבד',
        'מיועד לשני המינים'
    ];
    protected $program = [
        'תכנית אלול',
        'תכנית מלאה',
        'מדרשת שילוב',
    ];
    protected $target_audience = [
        'דתיות מבית',
        'בעלות תשובה ומתחזקות',
        'לנשים נשואות',
    ];
    protected $route_midrasha = [
        'קדם צבאי לבנות',
        'לפני שירות',
        'אחרי שירות',
    ];
    public function create()
    {
        $organizations = Organization::all();
        $types = JobType::all();
        $categories = Category::all();
        $areas = Area::all();
        $cities = City::all();
        $locations = Location::all();
        $addresses = Address::all();
        $stageOfEducations = StageOfEducation::all();
        $howToSort = $this->howToSort;
        $type_of_years = TypeOfYear::all();
        $nucleus = $this->nucleus;
        $job_for_list = $this->job_for_list;
        $programs = $this->program;
        $target_audience = $this->target_audience;
        $route_midrasha = $this->route_midrasha;
        return view('jobs.create', compact('organizations', 'types', 'categories', 'areas', 'cities', 'locations', 'addresses', 'stageOfEducations','howToSort','nucleus','type_of_years','job_for_list','programs','target_audience','route_midrasha'));
    }


    public function store(Request $request, $scraping = false)
    {
        $rules = [
            'title' => 'required|min:3',
//            'category_id' => 'required',
//            'subcategory_id' => 'required',
        ];

        $data = $request->all();
        if (isset($data['images']) && !$scraping) {
            $rules['images.*'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        $this->validate($request, $rules);

        $job = new Job();
        if ($scraping) {
            if (isset($data['out_identificator'])) {
                $job = Job::where('out_identificator', $data['out_identificator'])->first();
            } else {
                $job = Job::where('site', $data['site'])->where('title_old', $data['title'])->whereHas('hr', function ($query) use ($data) {
                    $query->whereIn('id',!is_array($data['hr_id']) ? [$data['hr_id']] : $data['hr_id']);
                })->where('created_at', '>', Carbon::now()->subMonth(4))->first();
            }

            if (!$job) {
                $job = new Job();
            } else {
                if ($job->is_admin_update) {
                    $this->exist_admin_change = true;
                }
            }
        } else {
            $job->is_admin_update = true;
        }
        if ($this->exist_admin_change) {
            if (isset($data['home']))
                $job->home = $data['home'];
            if (isset($data['out']))
                $job->out = $data['out'];
            if (isset($data['dormitory']))
                $job->out = $data['dormitory'];
            $job->save();
            return new JobResource($job);
        }
        if($scraping && $job->is_admin_update) {
            return;
        }
        $this->__main_control_block($data, $job, false, $scraping);
        return redirect(route('home'))->with('message', 'Job successfully created');
    }

    public function edit($id)
    {
        if (!$job = Job::find($id)) {
            abort(404);
        }
        $organizations = Organization::all();
        $types = JobType::all();
        $categories = Category::all();
        $areas = Area::all();
        $cities = City::all();
        $locations = Location::all();
        $addresses = Address::all();
        $stageOfEducations = StageOfEducation::all();
        $howToSort = $this->howToSort;
        $nucleus = $this->nucleus;
        $type_of_years = TypeOfYear::all();
        $job_for_list = $this->job_for_list;
        $programs = $this->program;
        $target_audience = $this->target_audience;
        $route_midrasha = $this->route_midrasha;
        return view('jobs.edit', compact('job', 'organizations', 'types', 'categories', 'areas', 'cities', 'locations', 'addresses', 'stageOfEducations', 'howToSort', 'nucleus','type_of_years','job_for_list','programs','target_audience','route_midrasha'));
    }

    public function update(Request $request, $id)
    {
        if (!$job = Job::find($id)) {
            abort(404);
        }
        $job->is_admin_update = true;

        $rules = [
            'title' => 'required|min:3',
            'category_id' => 'required',
            'subcategory_id' => 'required',
        ];
        $data = $request->all();
        if (isset($data['images'])) {
            $rules['images.*'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        $this->validate($request, $rules);
        $this->__main_control_block($data, $job, true, false);
        return redirect(route('home'))->with('message', 'Job successfully updated');
    }

    private function __main_control_block($data, $job, $is_update, $scraping)
    {
        if($scraping){
            if($job->title)
                $old_title = $job->title;
            else $old_title = $data['title'];
        }

        unset($data['_method']);
        unset($data['_token']);
        unset($data['area_id']);
        unset($data['type_id']);
        if(!$scraping) {
            if (isset($data['images'])) {
                $fileNames = $this->__files_control_block($data['images'], null);
                $data['images'] = $fileNames;
            }
        }
        if(isset($data['last_date_for_registration'])) {
            $data['last_date_for_registration'] = (new Carbon($data['last_date_for_registration']))->format('Y-m-d');
        }
        if(!isset($data['job_for']) || empty($data['job_for'])) {
            $data['job_for'] = "מיועד לבנות בלבד";
        }
        foreach ($data as $key => $value) {
            if ($key != 'images' && $key != 'hr_id' && $key != 'files' && $key != 'midrasha_info' && $key != 'type_of_year' && $key != 'organization_route_ids') {
                $job->$key = is_array($value) ? json_encode($value) : $value;
                if($key == 'organization_id') {
                    if($scraping) {
                        $orgRoutes = OrganizationRoute::where('organization_id',$value)->first();
                        if($orgRoutes) {
                            $job->organizationRoute()->sync($orgRoutes->id);
                        }
                    }
                }
            } else {
                if($key == 'organization_route_ids') {
                    if(is_array($value)) {
                        $job->organizationRoute()->sync($value);
                    }
                }
            }
        }
        if(!isset($data['active'])) {
            $job->active = 0;
        }
        if(!isset($data['checked'])) {
            $job->checked = 0;
        }
        if (!empty($data['type_of_year'])) {
            $job->type_of_year()->sync($data['type_of_year']);
        }
        if($scraping){
            $job->title = $old_title;
            $job->title_old = $old_title;
        }
        $job->save();
        if (isset($data['midrasha_info'])) {
            $this->saveMidrashaInfo($job, $data['midrasha_info']);
        }
        if (isset($data['images']) && !$scraping) {
            $job->images()->delete();
            foreach($job->images() as $image) {
                if (file_exists(storage_path('app/public/jobs/' . $image->file))) {
                    unlink(storage_path('app/public/jobs/' . $image->file));
                }
            }
            foreach ($data['images'] as $image) {
                $jobImage = new JobImage();
                $jobImage->job_id = $job->id;
                $jobImage->file = $image;
                $jobImage->save();
            }
        }
        if (isset($data['hr_id'])) {
            try {
                if (!$job->hr()->whereIn('id', !is_array($data['hr_id']) ? [$data['hr_id']] : $data['hr_id'])->count()) {
                    $job->hr()->attach(!is_array($data['hr_id']) ? [$data['hr_id']] : $data['hr_id']);
                }else{
                    $job->hr()->sync($data['hr_id']);
                }
            } catch (\Exception $exception) {

            }
        }
    }

    private function saveMidrashaInfo($job, $data)
    {
        if(JobMidrashaInfo::where('job_id',$job->id)->count()) {
            $midrasha = JobMidrashaInfo::where('job_id',$job->id)->first();
        }else{
            $midrasha = new JobMidrashaInfo();
        }
        $midrasha->job_id = $job->id;
        $midrasha->categories = is_array($data['categories']) ? json_encode($data['categories']) : $data['categories'];
        $midrasha->times = is_array($data['times']) ? json_encode($data['times']) : $data['times'];
        if(isset($data['times_timezone']))
        $midrasha->times_timezone = $data['times_timezone'];
        $midrasha->main_info = is_array($data['main_info']) ? json_encode($data['main_info']) : $data['main_info'];
        $midrasha->areas = $data['areas'];
        $midrasha->terms = is_array($data['terms']) ? json_encode($data['terms']) : $data['terms'];
        $midrasha->save();
    }

    private function __files_control_block($files, $job = null)
    {
        if ($job) {

        }
        $fileNames = [];
        foreach ($files as $file) {
            $fileName = time() . '.' . $file->extension();
            $fileNames[] = $fileName;
            $file->move(storage_path('app/public/jobs'), $fileName);
        }
        return $fileNames;
    }

    public function storeFiles(Request $request)
    {
        $rules = [
            'files' => 'required|array',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $myfile = time();
        \Session::put('imageStoreFile', $myfile);
        \Storage::disk('local')->put($myfile, json_encode($data['files']));
        return response()->json($data['files']);
    }

    public function getFiles()
    {
        dd(\Storage::disk('local')->get(\Session::get('imageStoreFile')));

    }

    public function removeFile($id)
    {
        $file = JobImage::find($id);
        if (file_exists('/storage/jobs/' . $file->file)) {
            unlink('/storage/jobs/' . $file->file);
        }
        $file->delete();
        return response()->json(true);
    }

    public function checked(Request $request, $id)
    {
        $job = Job::find($id);
        $data = $request->all();
        $job->checked = $data['checked'];
        $job->save();
    }

    public function destroy($id)
    {
        $job = Job::find($id);
        if(!$job) {
            return redirect()->back()->with('message','Job Not Found');
        }
        $job->delete();
        return redirect()->back()->with('message', 'Job successfully removed');
    }
}
