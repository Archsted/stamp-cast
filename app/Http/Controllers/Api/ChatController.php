<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Events\ChatReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatRequest;

class ChatController extends Controller
{
    public function store(StoreChatRequest $request) {
        $chat = Chat::create($request->all());
        broadcast(new ChatReceived($chat));
        return response('ok');
    }
}
