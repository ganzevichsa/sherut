<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsersResource;
use App\Http\Resources\UserTypes;
use App\Job;
use App\Organization;
use App\Role;
use App\Traits\GlobalLines;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
class UsersController extends Controller
{
    use GlobalLines;

    public function __construct()
    {
        $this->account_sid = env('TWILIO_SID', 'ACf6a0ea2615ba914dc63530f5793e5f78');
        $this->auth_token = env('TWILIO_AUTH_TOKEN', 'e5cceffe85a3e132c9ec4f1644fd4153');
        $this->twilio_number = env('TWILIO_NUMBER', '+972527091283');
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request, $scraping = false)
    {
        if ($request->ajax()) {
            $rules = [
                'name' => 'required|min:2|max:50',
                'organization_id' => 'required',
                'phone' => 'required|unique:users,phone',
            ];
        } else {
            $rules = [
//            'name' => 'required|min:2|max:50',
                'role_id' => 'required',
            ];
        }
        if (!$scraping) {
            $rules['phone'] = 'required|unique:users,phone';
        }
        $data = $request->all();

        $access = false;
        if (!empty($data['phone'])) {
            $rules['phone'] = 'required|unique:users,phone';
            $access = true;
            if ($scraping) {
                if (User::where('phone', $data['phone'])->count()) {
                    return User::where('phone', $data['phone'])->first();
                }
            }
        }
        if (!empty($data['email'])) {
            $rules['email'] = 'required|string|email|max:255|unique:users';
            $access = true;
            if ($scraping) {
                if (User::where('email', $data['email'])->count()) {
                    return User::where('email', $data['email'])->first();
                }
            }
        }

        if (isset($data['avatar'])) {
            $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        $this->validate($request, $rules);
        if (!$access) {
            return false;
        }

        if ($request->ajax()) {
            return response()->json($this->_main_control_block($request));
        }

        if($scraping && $data['phone']){
            $digitCode = "You have created an account in the application - https://play.google.com/store/apps/details?id=com.Bekalut.Sherut&gl=IL";
            $this->sendSMSNotification($data['phone'], $digitCode);
        }

        if (!$scraping) {
            $this->_main_control_block($request);
            return redirect()->back()->with('message', 'User successfully created');
        }

        return $this->_main_control_block($request);

    }

    private function sendSMSNotification($to = '', $body = '')
    {
        // Twillio SMS
        $client = new Client($this->account_sid, $this->auth_token);
        $client->messages->create($to, ['from' => $this->twilio_number, 'body' => $body]);
    }

    public function create()
    {
        $roles = Role::all();
        $organizations = Organization::all();
        $jobs = Job::all();
        return view('users.create', compact('roles', 'organizations', 'jobs'));
    }

    public function update(Request $request, $id, $scraping = null)
    {
//        $rules = [
//            'name' => 'required|min:2|max:50',
//            'role_id' => 'required',
//        ];
        $rules['phone'] = 'required|string|numeric|unique:users,phone,' . $id;
        if (!empty($data['email'])) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $id;
        }
        $this->validate($request, $rules);
        $user = User::find($id);
        if(!$user) {
            abort(404);
        }
        $this->_main_control_block($request, $user);
        return redirect(route('users.index'))->with('message', 'User successfully updated');
    }

    public function edit($id)
    {
        if(!$user = User::find($id)) {
            return abort(404);
        }
        $roles = Role::all();
        $organizations = Organization::all();
        $jobs = Job::all();
        return view('users.edit', compact('user', 'roles', 'organizations', 'jobs'));
    }

    public function storeJob($user, $job_ids)
    {
        $user->jobs()->sync($job_ids);
    }

    private function _main_control_block($request, $user = null)
    {
        $data = $request->all();
        if (!$user)
            $user = new User();
        if (isset($data['name']))
            $user->name = $data['name'];
        if (isset($data['role_id']))
            $user->role_id = $data['role_id'];
//        if (isset($data['password']))
        $user->password = bcrypt(rand(6, 8));
        if (isset($data['email']))
            $user->email = $data['email'];
        if (isset($data['phone']))
            $user->phone = $data['phone'];
        if (isset($data['organization_id']) || isset($data['organization']))
            $user->organization_id = isset($data['organization_id']) ? $data['organization_id'] : $data['organization'];

        if (isset($data['job_ids'])){
            $user->jobs()->sync(array_merge($data['job_ids'],$user->jobs()->pluck('id')->toArray()));
        }
        if (isset($data['avatar'])) {
            $this->_uploadImageControl($data['avatar'], $user, 'avatar', 'app/public/users/avatars/');
        }
        $user->save();

        return $user;
    }

    public function getSimpleTypes()
    {
        return UserTypes::collection(Role::where('is_simple_user', 1)->get());
    }

    public function saveType($role_id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }
        $user->role_id = $role_id;
        $user->save();
        return response()->json(new UsersResource($user), 201);
    }


}
