<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewsResource;
use App\Http\Resources\UsersResource;
use App\Job;
use App\JobReview;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobReviewsController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function index($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'not_found'], 404);
        }
        $reviews = JobReview::where('job_id',$id)->where('status', 0)->get();
        return [
            'title' => $job->title,
            'cover_image' => $job->images()->count() ? '/storage/jobs/' . $job->images()->first()->file : false,
            'logo' => $job->organization->logo ? '/storage/organizations/logos/' . $job->organization->logo : false,
            'stars' => $job->reviews()->count() ? round($job->reviews()->sum('stars') / $job->reviews()->count()) : 0,
            'count' => $reviews->count(),
            'data' => ReviewsResource::collection($reviews)
        ];
    }

    public function store(Request $request, $id)
    {
        $rules = [
            'description' => 'required|min:3',
        ];
        $this->validate($request, $rules);
        if(!Job::find($id)) {
            return response()->json(['message' => 'not_found'],404);
        }
        $data = $request->all();
        $review = new JobReview();
        $review->user_id = $this->user->id;
        $review->job_id = $id;
        $review->first_name = $data['first_name'];
        $review->last_name = $data['last_name'];
        $review->phone = $data['phone'];
        $review->show_info = $data['show_info'];
        $review->description = $data['description'];
        $review->stars = $data['stars'];
        $review->date = $data['date'];
        $review->duration = $data['duration'];

        if (isset($data['avatar']) && !empty($data['avatar'])) {
            $review->avatar = $this->user->uploadAvatar($data['avatar']);
        }
        $review->save();
        return response()->json(['message' => 'success'],200);
    }

    public function getData($id)
    {
        $dates = [];
        for ($i = date('Y'); $i > Carbon::now()->subYears(20)->format('Y'); $i--) {
            $dates[] = $i;
        }
        return response()->json(
            [
                'user_info' => new UsersResource($this->user),
                'show_info' => [
                    1 => 'פרסום חוות הדעת עם הפרטים שלי',
                    0 => 'פרסום חוות הדעת ללא הפרטים שלי'
                ],
                'dates' => $dates,
            ]
        );
    }
}
