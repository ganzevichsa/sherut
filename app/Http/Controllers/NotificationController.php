<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Events\SendNotificationAdmin;

class NotificationController extends Controller
{
    public function index (Request $request)
    {
    	if($request->method() == 'POST'){
    		try{
    			$notification = Notification::create(['user' => 'all', 'message' => $request->message, 'type' => 'admin']);

    			broadcast(new SendNotificationAdmin($notification));

    			return back()->with('message', 'Notification send.');
    		}catch(\Exception $ex){
    			dd($ex);
    			return back()->with('message', 'Error: '.$ex->message());
    		}
    		
    	}

    	return view('notification.index');
    }

    public function getList (Request $request)
    {
    	if($request->list == 'all'){
    		try{
    			$results = Notification::where('send', 'NO')->where('type', $request->type)->get();
    			return response()->json(array('status' => true, 'data' => $results), 200);
    		}catch(\Exception $ex){
    			return response()->json(array('status' => false, 'error' => $ex->message()), 500);
    		}
    	}

    	return response()->json(array('status' => true), 404);
    }
}
