<?php

namespace App\Events;

use App\Chat;
use App\Room;
use App\Stamp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class ChatReceived implements ShouldBroadcast
{
    private $name;
    private $message;
    private $hash;

    private $chat;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('room.1');
    }

    /**
     * ブロードキャストするデータだけ抽出
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'chat' => [
                'id' => $this->chat->id,
                'name' => $this->chat->name,
                'color' => $this->chat->color,
                'message' => $this->chat->message,
                'created_at' => $this->chat->created_at,
            ],
        ];
    }
}
