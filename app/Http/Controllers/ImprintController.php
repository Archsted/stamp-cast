<?php

namespace App\Http\Controllers;

use App\Events\StampEvent;
use App\Http\Requests\StoreImprint;
use App\Http\Requests\StoreImprintGuest;
use App\Imprint;
use App\Room;
use Illuminate\Http\Request;

class ImprintController extends Controller
{
    // ログインユーザによるスタンプ送信
    public function create(Room $room, StoreImprint $request)
    {
        event(new StampEvent($request->stamp_id, $room));

        $imprint = Imprint::create([
            'room_id' => $room->id,
            'user_id' => $request->user()->id, // ログイン済みの場合
            'stamp_id' => $request->stamp_id,
            'ip' => $request->ip(),
        ]);
    }

    // 未ログインユーザによるスタンプ送信
    public function guestCreate(Room $room, StoreImprintGuest $request)
    {
        event(new StampEvent($request->stamp_id, $room));

        $imprint = Imprint::create([
            'room_id' => $room->id,
            'user_id' => null, // 未ログインの場合
            'stamp_id' => $request->stamp_id,
            'ip' => $request->ip(),
        ]);
    }
}
