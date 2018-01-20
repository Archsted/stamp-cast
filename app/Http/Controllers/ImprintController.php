<?php

namespace App\Http\Controllers;

use App\Events\StampEvent;
use App\Http\Requests\StoreImprint;
use App\Imprint;
use App\Room;
use Illuminate\Http\Request;

class ImprintController extends Controller
{
    public function create(Room $room, StoreImprint $request)
    {
        event(new StampEvent($request->stamp_id, $room));

        $imprint = Imprint::create([
            'room_id' => $room->id,
            'user_id' => $request->user_id,
            'stamp_id' => $request->stamp_id,
            'ip' => $request->ip(),
        ]);
    }
}
