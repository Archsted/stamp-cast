<?php

namespace App\Http\Controllers;

use App\Events\StampEvent;
use App\Http\Requests\StoreImprint;
use App\Room;
use Illuminate\Http\Request;

class ImprintController extends Controller
{
    public function create(Room $room, StoreImprint $request)
    {
        event(new StampEvent($request->stamp_id, $room));
    }
}
