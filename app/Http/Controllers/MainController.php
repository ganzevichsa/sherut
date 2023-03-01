<?php

namespace App\Http\Controllers;

use App\ContactUs;
use App\Mail\SendEmailContacUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function contactus(Request $request)
    {
        $rules = [
            'title' => 'required|min:3',
            'description' => 'required|min:3',
            'phone' => 'required',
            'email' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();
        $contact = new ContactUs();
        $contact->title = $data['title'];
        $contact->phone = $data['phone'];
        $contact->email = $data['email'];
        $contact->description = $data['description'];
        $contact->save();

        // Mail::send('email.contactus', ['data' => $data], function($message) use ($data)
        // {
        //     $message->to('sherutbekalut@gmail.com')->subject($data['title']);
        // });

        return response()->json(['message' => 'success'],200);
    }
}
