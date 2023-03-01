<?php

namespace App\Http\Controllers;

use App\Chat;
use App\ChatMessage;
use App\Events\Chat as ChatEvent;
use App\Events\NewMessageAdded;

use App\Http\Resources\ConversationResrouce;
use App\Http\Resources\MessagesResource;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function conversations()
    {
        if ($this->user->role_id != Role::HR) {
            $chat = Chat::where('user_id', $this->user->id)->orderBy('created_at', 'desc')->get();
        } else {
            $chat = Chat::where('hr_id', $this->user->id)->orderBy('created_at', 'desc')->get();
        }
        return ConversationResrouce::collection($chat);
    }

    public function messages($id)
    {
        if ($this->user->role_id != Role::HR) {
            $chat = Chat::where('id', $id)->where('user_id', $this->user->id)->orderBy('created_at', 'desc')->first();
        } else {
            $chat = Chat::where('id', $id)->where('hr_id', $this->user->id)->orderBy('created_at', 'desc')->first();
        }
        if (empty($chat)) {
            return response()->json(['message' => 'not_found'], 404);
        }
        return MessagesResource::collection($chat->messages);
    }

    public function open($user_id)
    {
        if (!User::where('id', $user_id)->first()) {
            return response()->json(['message' => 'user_not_found'], 404);
        }
        if (!$chat = Chat::where('hr_id', $this->user->id)->where('user_id', $user_id)->first()) {
            $chat = new Chat();
            $chat->hr_id = $this->user->id;
            $chat->user_id = $user_id;
            $chat->save();
        }
        return ['messages' => MessagesResource::collection($chat->messages), 'id' => $chat->id];
    }

    public function store(Request $request, $id)
    {
        $rules['message'] = 'required';
     
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if(!$chat = Chat::find($id)) {
            return response()->json(['message' => 'not_found'], 404);
        }
        $message = new ChatMessage();
        $message->chat_id = $id;
        $message->user_id = $this->user->id;
        $message->message = $data['message'];
        $message->save();

        // $redis = Redis::connection();
        
        // $data = ['message' => $message->message, 'chat_id' => $message->chat_id];
        
        // $redis->publish('message', json_encode($data));
        broadcast(new NewMessageAdded($message));
        return new MessagesResource($message);
    }
}
