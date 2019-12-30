<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $room = auth()->user()->room;

        return view('home', compact('room'));
    }

    public function apps()
    {
        return view('user.apps');
    }

    public function listener(Room $room, $tag = null)
    {
        $noTags = 'false';

        return view('listener', compact('room', 'tag', 'noTags'));
    }

    public function listenerNoTags(Room $room)
    {
        $tag = null;
        $noTags = 'true';

        return view('listener', compact('room', 'tag', 'noTags'));
    }

    public function setSendRoom(Room $room)
    {
        // アップロード不可
        if ($room->uploader_level === Room::UPLOADER_LEVEL_NOBODY) {
            abort(403, 'このルームは送信先固定機能をご利用になれません。');
        }

        // ユーザーでないと不可
        if ($room->uploader_level === Room::UPLOADER_LEVEL_USER_ONLY && !request()->user()) {
            abort(403, 'このルームでは未登録ユーザーは固定機能をご利用になれません。');
        }

        // 対象ルームを設定
        session([
            'send.room.id' => $room->id,
            'send.room.name' => $room->name,
        ]);

        return redirect()->back();
    }

    public function clearSendRoom()
    {
        session()->forget('send');

        return redirect()->back();
    }

    public function broadcaster(Room $room)
    {
        return view('broadcaster', compact('room'));
    }

    public function broadcasterFluid(Room $room)
    {
        return view('broadcaster_fluid', compact('room'));
    }
}
