<?php

namespace App\Http\Controllers;

use App\Events\StampEvent;
use App\Http\Requests\StoreImprint;
use App\Http\Requests\StoreImprintGuest;
use App\Imprint;
use App\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ImprintController extends Controller
{
    public function index(Room $room)
    {
        if ($room->user_id !== request()->user()->id) {
            abort(403, 'ルームの持ち主以外はアクセスできません。');
        }

        $imprints = $room->imprints()
            ->selectRaw('max(id) as imprint_id, count(*) as cnt, stamp_id, user_id, ip')
            ->where('created_at', '>', Carbon::now()->subMinutes( env('IMPRINTS_LOG_MINUTE', 5 )))
            ->groupBy(['stamp_id', 'user_id', 'ip'])
            ->with(['stamp' => function ($query) {
                $query->select(['id', 'name', 'thumbnail', 'is_animation'])
                    ->withoutGlobalScope('softDelete');
            }])
            ->get();

        $list = [];
        foreach ($imprints as $imprint) {
            $key = $imprint['user_id'] ?: $imprint['ip'];

            if (empty($list[$key])) {
                $list[$key] = [];
            };

            $list[$key][] = [
                'id' => $imprint['imprint_id'],
                'count' => $imprint['cnt'],
                'stamp' => $imprint->stamp->toArray()
            ];
        }

        $list = array_values($list);

        return view('imprint.index', compact('list'));
    }

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
