<?php

namespace App\Http\Controllers;

use App\Faq;
use App\FaqAnswer;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FaqHrJobsQuestionsResource;
use App\Http\Resources\FaqHrQuestionsResource;
use App\Job;
use App\JobHr;
use App\Role;
use App\User;
use App\UserJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function index(Request $request, $job_id)
    {

        $data = $request->all();

        $job = Job::find($job_id);
        if (!$job) {
            return response()->json(['message' => 'Not found'], 404);
        }
        if(!empty($data['search'])) {
            $faq = Faq::where('status', Faq::ACCEPTED)->where('job_id', $job->id)->where('question','LIKE','%'.$data['search'].'%')->orderBy('created_at', 'DESC')->get();
        } else {
            $faq = Faq::where('status', Faq::ACCEPTED)->where('job_id', $job->id)->orderBy('created_at', 'DESC')->get();
        }
        return FaqResource::collection($faq);
    }

    public function store(Request $request, $job_id)
    {

        $rules = [
            'question' => 'required|min:3',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $faq = new Faq();
        $faq->user_id = $this->user->id;
        $faq->job_id = $job_id;
        $faq->question = $data['question'];
        $faq->hr_id = $this->user->role_id == 2 ? $this->user->id : null;
        $faq->save();
        return response()->json(['message' => 'שאלתך נשלחה'], 200);
    }

    public function answer(Request $request, $job_id, $id)
    {
        $rules = [
            'answer' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['message' => 'Not found'], 404);
        }
        if ($this->user->role_id != Role::HR) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $faq_answer = FaqAnswer::where('faq_id', $id)->first();
        if(isset($faq_answer)){
            $faq_answer->answer = $data['answer'];
            $faq_answer->save();
        }
        else{
            $answer = new FaqAnswer();
            $answer->faq_id = $faq->id;
            $answer->status = 1;
            $answer->answer = $data['answer'];
            $answer->save();
            $faq->status = 1;
            $faq->hr_id = $this->user->id;
            $faq->save();
        }

        return response()->json(['message' => ''], 200); // TODO add message
    }

    public function answerBack(Request $request, $job_id, $id)
    {
        $rules = [
            'answer' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        $answer = new FaqAnswer();
        $answer->faq_id = $faq->id;
        if ($this->user->role_id == Role::HR) {
            $answer->status = 1;
            $lastQuestion = $faq->answers()->where('is_hr',0)->orderBy('created_at','DESC')->first();
            if($lastQuestion) {
                $lastQuestion->status = 1;
                $lastQuestion->save();
            }
        } else {
            $answer->is_hr = 0;
        }
        $answer->answer = $data['answer'];
        $answer->save();
        return response()->json(['message' => ''], 200); // TODO add message
    }

    public function jobQuestion($id)
    {

        $faqs = Faq::where('job_id', $id)
            ->with('answers')
            ->orderBy('created_at', 'DESC')->get();

        return response()->json(['faqs' => $faqs], 200);

    }

    public function hrQuestion(Request $request)
    {

        $rules = [
            'hr_id' => 'required',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $faqs = Faq::where('hr_id', $request->hr_id)
            ->orderBy('created_at', 'DESC')->get();

        return ['faqs' => FaqHrQuestionsResource::collection($faqs)];

    }

    public function getHrJobsQuestions(Request $request)
    {

//        $request->validate([
//            'hr_id' => 'required',
//        ]);

        $hr_id = $request->hr_id;

        $userAuth = User::find($hr_id);

 
        if ($userAuth->role_id == 2) {

            $jobs = JobHr::where('user_id', $hr_id)->pluck('job_id');
            $jobsUsersId = UserJob::whereIn('job_id', $jobs)->pluck('user_id');
            $faqs = Faq::whereIn('user_id', $jobsUsersId)->get();
            
            return FaqHrJobsQuestionsResource::collection($faqs);

        } else {
            return response()->json(['faqs' => []], 200);
        }
    }
}
