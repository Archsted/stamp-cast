<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Events\ChatReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatRequest;

class ChatController extends Controller
{
    public function store(StoreChatRequest $request) {
        $chat = new Chat;

        //        $chat->user_id = null;
        $chat->name = $request->name;
        $chat->color = $request->color;
        $chat->message = $request->message;
        $chat->ip = $request->ip();
        $chat->save();

        broadcast(new ChatReceived($chat));
        return response('ok');
    }
}
