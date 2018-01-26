<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStamp;
use App\Http\Requests\StoreStampGuest;
use App\Room;
use App\Stamp;
use Illuminate\Http\Request;

class StampController extends Controller
{
    /**
     * スタンプ一覧の取得
     *
     * @return array
     */
    public function index(Room $room)
    {
        // 現在表示中のRoomに紐付いたStampか、何のルームにも紐付いていないStampを取得する
        $stamps = Stamp::query()
            ->where(function ($query) use ($room) {
                $query->where('room_id', $room->id)
                    ->orWhereNull('room_id');
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return [
            'stamps' => $stamps
        ];
    }

    /**
     * スタンプの登録（ログインユーザー）
     *
     * @param Room $room
     * @param StoreStamp $request
     * @return array
     */
    public function create(Room $room, StoreStamp $request)
    {
        $file = $request->file('file');

        $stamp = new Stamp;
        $stamp->user_id = $request->user()->id; // ログインユーザーID
        $stamp->room_id = $room->id;
        $stamp->name = $file->store('stamps');
        $stamp->size = $file->getSize();

        // 画像情報の取得
        $imageSize = getimagesize($file->getRealPath());
        $stamp->width = $imageSize[0];
        $stamp->height = $imageSize[1];
        $stamp->mime_type = $imageSize['mime'];

        $stamp->save();

        return [
            'stamp' => $stamp,
            'room' => $room
        ];
    }

    /**
     * スタンプの登録（ゲストユーザー）
     *
     * @param Room $room
     * @param StoreStampGuest $request
     * @return array
     */
    public function guestCreate(Room $room, StoreStampGuest $request)
    {
        $file = $request->file('file');

        $stamp = new Stamp;
        $stamp->room_id = $room->id;
        $stamp->name = $file->store('stamps');
        $stamp->size = $file->getSize();

        // 画像情報の取得
        $imageSize = getimagesize($file->getRealPath());
        $stamp->width = $imageSize[0];
        $stamp->height = $imageSize[1];
        $stamp->mime_type = $imageSize['mime'];

        $stamp->save();

        return [
            'stamp' => $stamp,
            'room' => $room
        ];
    }

    public function sample()
    {
        $stamps = Stamp::query()
            ->whereNull('room_id')
            ->orderBy('id')
            ->get();

        return [
            'stamps' => $stamps
        ];
    }
}
