<?php

namespace App\Http\Controllers;

use App\Address;
use App\Area;
use App\Category;
use App\Http\Resources\ContendersResource;
use App\Http\Resources\OpportunityChosenContendersResource;
use App\Http\Resources\OpportunityContender;
use App\Http\Resources\OpportunityEditMidrashaResource;
use App\Http\Resources\OpportunityEditResource;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\OpportunityShowResource;
use App\Http\Resources\SimpleTableResource;
use App\Http\Resources\SingleJobResource;
use App\Job;
use App\JobImage;
use App\JobType;
use App\Organization;
use App\OrganizationRoute;
use App\UserJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HrJobController extends Controller
{
    protected $user;
    protected $jobs;
    protected $job;
    public $places = [
        'home' => 'תקן בית',
        'out' => 'דירת שירות',
        'dormitory' => 'פנימיה'
    ];
    public $midrasha_places = [
        'home' => 'במדרשה',
        'out' => 'ללא לינה'
    ];
    protected $limit = 20;

    public function __construct()
    {
        $this->user = auth('web')->user();
        $this->jobs = $this->user->jobs();
    }

    public function updateStatus($id, $status)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            $job->status = $status;
            $job->save();
            return response()->json(['message' => 'success'], 200);
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function getContenders($id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            return ['contenders' => ContendersResource::collection($job->jobUsers()->orderBy('created_at', 'DESC')->get()), 'count_of_all_positions' => $job->count_of_all_positions,  'count_of_taken_positions' => $job->jobUsers()->where('status',UserJob::APPROVED)->count()];
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function opportunity($id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            return new OpportunityResource($job);
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function opportunityView($id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            return new OpportunityShowResource($job);
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function opportunityGetData($type)
    {
        $data = [];
        if ($type == JobType::MIDRASHA) {
            $data['is_midrashot'] = true;
            $data['program'] = [
                'תכנית אלול',
                'תכנית מלאה',
                'מדרשת שילוב',
            ];
            $data['target_audience'] = [
                'דתיות מבית',
                'בעלות תשובה ומתחזקות',
                'לנשים נשואות',
            ];
            $data['routes'] = [
                'קדם צבאי לבנות',
                'לפני שירות',
                'אחרי שירות',
            ];
            $data['places'] = $this->midrasha_places;
        } else {
            $data['is_midrashot'] = false;
            $data['categories'] = Category::all();
            $data['organizations'] = Organization::all();
            $data['nucleus'] = [
                'כן',
                'לא'
            ];
            $data['howToSort'] = [
                'מיונים מוקדמים',
                'שאלון העדפות',
                'סיירות רגילות'
            ];
            $data['job_for_list'] = [
                'מיועד לבנים בלבד',
                'מיועד לבנות בלבד',
                'מיועד לשני המינים'
            ];
            $data['routes'] = OrganizationRoute::all();
            $data['places'] = $this->places;
        }
        $data['areas'] = Area::all();
        return response()->json($data);
    }

    public function opportunityGetTypes()
    {
        return SimpleTableResource::collection(JobType::all());
    }


    public function opportunityStore(Request $request, $type)
    {
        if ($type == JobType::MIDRASHA) {
            $rules = $this->__midrasha_rules();
        } else {
            $rules = $this->__rules();
        }
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if ($type == JobType::MIDRASHA) {
            $job = $this->__main_control_block_midrasha($data);
        } else {
            $job = $this->__main_control_block($data);
        }
        $job->job_type_id = $type;
        $job->save();
        return response()->json(['message' => 'success'], 200);
    }

    public function opportunityGet($id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            if ($job->job_type_id == JobType::MIDRASHA) {
                return new OpportunityEditMidrashaResource($job);
            } else {
                return new OpportunityEditResource($job);
            }
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function opportunityUpdate(Request $request, $id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            if ($job->job_type_id == JobType::MIDRASHA) {
                $rules = $this->__midrasha_rules();
            } else {
                $rules = $this->__rules();
            }
            $data = $request->all();
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            if ($job->job_type_id == JobType::MIDRASHA) {
                $this->__main_control_block_midrasha($data, $job);
            } else {
                $this->__main_control_block($data, $job);
            }
            return response()->json(['message' => 'success'], 200);
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function getOpportunityChosenContenders($id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            return OpportunityChosenContendersResource::collection($job->jobUsers()->where('status', UserJob::APPROVED)->get());
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function getOpportunityContender($id, $contender_id)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            if ($contender = $job->jobUsers()->where('id', $contender_id)->first()) {
                return new OpportunityContender($contender);
            } else {
                return response()->json(['message' => 'not_found'], 404);
            }
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    public function opportunityContenderUpdateStatus($id, $contender_id, $status)
    {
        if ($job = $this->jobs->where('jobs.id', $id)->first()) {
            if ($contender = $job->jobUsers()->where('id', $contender_id)->first()) {
                $contender->status = $status;
                $contender->save();
                return response()->json(['message' => 'success'], 200);
            } else {
                return response()->json(['message' => 'not_found'], 404);
            }
        }
        return response()->json(['message' => 'not_found'], 404);
    }

    private function __main_control_block($data, $job = null)
    {
        if (!$job) {
            $job = new Job();
        }
        $job->title = $data['title'];
        $job->category_id = $data['category_id'];
        if (!empty($data['subcategory_id'])) {
            $job->subcategory_id = $data['subcategory_id'];
        }
        if (!empty($data['job_for'])) {
            $job->job_for = $data['job_for'];
        }
        if (!empty($data['description'])) {
            $job->description = $data['description'];
        }
        $job->city_id = $data['city_id'];
        if (!empty($data['address'])) {
            $address = Address::where('name', $data['address'])->first();
            if (!$address) {
                $address = new Address();
                $address->name = $data['address'];
                $address->save();
            }
            $job->address_id = $address->id;
        }
        $place = $data['place'];
        $job->$place = $data['count'];
        if (!empty($data['nucleus'])) {
            $job->nucleus = $data['nucleus'];
        }
        if (!empty($data['how_to_sort'])) {
            $job->how_to_sort = $data['how_to_sort'];
        }
        if (!empty($data['organization_id'])) {
            $job->organization_id = $data['organization_id'];
        }
        if (!empty($data['video_url'])) {
            $job->video_url = $data['video_url'];
        }
        $job->last_date_for_registration = $data['last_date_for_registration'];
        if (!empty($data['other_hr_name'])) {
            $job->other_hr_name = $data['other_hr_name'];
        }
        if (!empty($data['other_hr_phone'])) {
            $job->other_hr_phone = $data['other_hr_phone'];
        }
        $job->save();
        if (!empty($data['route_id']) && is_array($data['route_id'])) {
            $job->organizationRoute()->sync($data['route_id']);
        }
        $job->hr()->attach($this->user->id);
        if (isset($data['images'])) {
            $fileNames = $this->__files_control_block($data['images']);
            $data['images'] = $fileNames;
            $job->images()->delete();
            foreach ($job->images() as $image) {
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
        return $job;
    }

    public function __main_control_block_midrasha($data, $job = null)
    {
        if (!$job) {
            $job = new Job();
        }
        $job->title = $data['title'];
        $job->city_id = $data['city_id'];
        if (!empty($data['address'])) {
            $address = Address::where('name', $data['address'])->first();
            if (!$address) {
                $address = new Address();
                $address->name = $data['address'];
                $address->save();
            }
            $job->address_id = $address->id;
        }
        $job->program = $data['city_id'];
        $place = $data['place'];
        $job->$place = $data['count'];
        if (!empty($data['route_midrasha'])) {
            $job->route_midrasha = $data['route'];
        }
        if (!empty($data['target_audience'])) {
            $job->target_audience = $data['target_audience'];
        }
        if (!empty($data['main_areas_of_study'])) {
            $job->route_midrasha = $data['main_areas_of_study'];
        }
        if (!empty($data['description'])) {
            $job->description = $data['description'];
        }
        if (!empty($data['video_url'])) {
            $job->video_url = $data['video_url'];
        }
        if (!empty($data['other_hr_name'])) {
            $job->other_hr_name = $data['other_hr_name'];
        }
        if (!empty($data['other_hr_phone'])) {
            $job->other_hr_phone = $data['other_hr_phone'];
        }
        $job->save();
        $job->hr()->attach($this->user->id);
        if (isset($data['images'])) {
            $fileNames = $this->__files_control_block($data['images']);
            $data['images'] = $fileNames;
            $job->images()->delete();
            foreach ($job->images() as $image) {
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
        return $job;

    }

    private function __files_control_block($files)
    {
        $fileNames = [];
        foreach ($files as $file) {
            $fileName = time() . '.' . $file->extension();
            $fileNames[] = $fileName;
            $file->move(storage_path('app/public/jobs'), $fileName);
        }
        return $fileNames;
    }

    private function __rules()
    {
        $rules['title'] = 'required|min:3';
        $rules['place'] = 'required';
        $rules['count'] = 'required|numeric';
        $rules['category_id'] = 'required';
        $rules['area_id'] = 'required';
        $rules['city_id'] = 'required';
        $rules['last_date_for_registration'] = 'required|date';
        if (isset($data['images'])) {
            $rules['images.*'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        return $rules;
    }

    private function __midrasha_rules()
    {
        $rules['title'] = 'required|min:3';
        $rules['place'] = 'required';
        $rules['count'] = 'required|numeric';
        $rules['area_id'] = 'required';
        $rules['city_id'] = 'required';
        $rules['target_audience'] = 'required';
        $rules['route'] = 'required';
        $rules['program'] = 'required';
        if (isset($data['images'])) {
            $rules['images.*'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        return $rules;
    }
}
