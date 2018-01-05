<?php

namespace App\Events;

use App\Room;
use App\Stamp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StampEvent implements ShouldBroadcast
{
    public $stamp;

    private $room; // スタンプ送り元ルーム

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($stamp_id, Room $room)
    {
        $stamp = Stamp::findOrFail($stamp_id);

        $this->stamp = $stamp;
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
            'stamp' => [
                'id' => $this->stamp->id,
                'name' => $this->stamp->name,
                'width' => $this->stamp->width,
                'height' => $this->stamp->height,
            ],
        ];
    }
}
