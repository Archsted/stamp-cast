<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function create()
    {
        return view('room.create');
    }

    public function store(StoreRoomRequest $request)
    {
        // 新規登録
        $room = Room::create([
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'imprinter_level' => $request->imprinter_level,
            'uploader_level' => $request->uploader_level,
        ]);

        return redirect()->route('listener', ['room' => $room->id]);
    }

    public function edit(Room $room)
    {
        // ルームの作成者以外の場合はエラーとする
        if (auth()->user()->id !== $room->user_id) {
            abort(403);
        }

        return view('room.edit', compact('room'));
    }

    public function update(Room $room, UpdateRoomRequest $request)
    {
        $room->update($request->only([
            'name',
            'description',
            'imprinter_level',
            'uploader_level',
        ]));

        return redirect()->route('listener', ['room' => $room->id]);
    }
}
