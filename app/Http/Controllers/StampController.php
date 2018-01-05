<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStamp;
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
            ->get();

        return [
            'stamps' => $stamps
        ];
    }

    /**
     * スタンプの登録
     *
     * @param StoreStamp $request
     * @return array
     */
    public function create(Room $room, StoreStamp $request)
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
}
