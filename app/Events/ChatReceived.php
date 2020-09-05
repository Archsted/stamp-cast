<?php

namespace App\Events;

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

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($name, $message, $hash)
    {
        $this->name = $name;
        $this->message = $message;
        $this->hash = $hash;
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
                'name' => $this->name,
                'message' => $this->message,
                'timestamp' => now(),
                'hash' => $this->hash,
                'key' => Str::random(40)
            ],
        ];
    }
}
