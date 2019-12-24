<?php

namespace App\Events;

use App\Imprint;
use App\Room;
use App\Stamp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TextStampEvent implements ShouldBroadcast
{
    public $imprint;

    private $room; // スタンプ送り元ルーム

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Imprint $imprint, Room $room)
    {
        $this->imprint = $imprint;
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('room.' . $this->room->id);
    }

    /**
     * ブロードキャストするデータだけ抽出
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'imprint' => [
                'user' => $this->imprint->user_id ? $this->imprint->user->name : null,
                'comment' => $this->imprint->comment,
            ],
        ];
    }
}
