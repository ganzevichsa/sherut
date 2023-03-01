<?php

namespace App\Http\Controllers;

use App\Http\Resources\HrResource;
use App\Http\Resources\UsersResource;
use App\Organization;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Twilio\Rest\Client;

class HrController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function account()
    {
        return new HrResource($this->user);
    }

    public function accountStore(Request $request)
    {
        $rules = [
            'phone' => 'required|string|numeric',
        ];
        if (!empty($data['email'])) {
            $rules['email'] = 'required|email|unique:email,users,' . $this->user->id;
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();

        if ($data['phone'] != $this->user->phone) {
            $digitCode = $this->generatePIN(4);
            \Log::info($digitCode);
            // $this->sendSMSNotification($this->user->phone, $digitCode);
            
            $this->user->accountDigitCode = $digitCode;
            $this->user->save();

            return response(['type' => 'sms', 'message' => 'success'], 200);
            
        }
      
        $this->_main_control_block($data);
        return new HrResource($this->user);
    }

    public function activateNewNumber(Request $request, $digit_code)
    {
        $rules['code'] = 'digits:4';
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if ($this->user->accountDigitCode != $digit_code) {
            return response()->json(['user_not_found'], 404);
        }
        $this->user->accountDigitCode = '';
        $this->user->save();
        $this->_main_control_block($data);
        return new HrResource($this->user);
    }

    private function _main_control_block($data)
    {
        if(!empty($data['first_name'])){
            $this->user->first_name = $data['first_name'];
        }
        if(!empty($data['last_name'])){
            $this->user->last_name = $data['last_name'];
            
        }

        if (isset($data['avatar']) && !empty($data['avatar'])) {
            if ($this->user->avatar) {
                unlink(storage_path('app/public/users/avatars/' . $this->user->avatar));
            }
            $this->user->avatar = $this->user->uploadAvatar($data['avatar']);
        }
        $organization = $this->user->organization;
        if(!empty($data['organization_name'])){
            if (!$this->user->organization || $this->user->organization->name != $data['organization_name']) {
                if (!$organization = Organization::where('name', $data['organization_name'])->first()) {
                    $organization = new Organization();
                }
                $organization->name = $data['organization_name'];
                $organization->save();
            }
            $this->user->organization_id = $organization->id;
        }

        $this->user->phone = $data['phone'];
        if(!empty($data['areas'])){
            $this->user->areas()->sync($data['areas']);
        }
        if(!empty($data['email'])){
            $this->user->email = $data['email'];
        }
        if(!empty($data['about'])){
            $this->user->about = $data['about'];
        }
        
        $this->user->save();
    }

    private function sendSMSNotification($to = '', $body = '')
    {
        // Twillio SMS
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($to,
            ['from' => $twilio_number, 'body' => $body]);
    }

    private function generatePIN($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        if (strlen($pin) != 4) {
            $this->generatePIN();
        }
        if (User::where('digit_number', $pin)->first()) {
            $this->generatePIN();
        }
        return $pin;
    }

}
