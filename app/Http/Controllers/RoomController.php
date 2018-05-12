<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Room;
use App\StampTag;
use App\Tag;
use App\User;
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

    public function indexTagNamesWithCount(Room $room, Request $request)
    {
        /** @var User $roomOwner */
        $roomOwner = $room->user;

        $blackListIps = $roomOwner->blackListIps->pluck('ip');
        $blackListUserIds = $roomOwner->blackListUsers->pluck('id');

        $query = StampTag::query()
            ->where('room_id', $room->id)
            ->whereHas('stamp', function ($sql) use ($blackListIps, $blackListUserIds, $request, $room) {
                $sql->withoutBlackList($blackListIps, $blackListUserIds);

                if ($request->bookId) {
                    $sql->whereHas('books', function ($sql) use ($request) {
                        $sql->where('books.id', $request->bookId);
                    });

                    // アップロード禁止設定の場合、ルームに属すスタンプかnullのものだけ返す
                    if ($room->uploader_level === Room::UPLOADER_LEVEL_NOBODY) {
                        $sql->where(function ($sql) use ($room) {
                            $sql->where('room_id', $room->id)
                                ->orWhereNull('room_id');
                        });
                    }
                }
            })
            ->groupBy('tag_id')
            ->selectRaw('tag_id, count(*) as cnt')
            ->orderBy('cnt', 'desc')
            ->with('tag');

        $stampTags = $query->get();

        $tagList = [];

        foreach ($stampTags as $stampTag) {
            $tagList[] = [
                'text' => $stampTag->tag->text,
                'count' => $stampTag->cnt,
            ];
        }

        return $tagList;
    }

    public function indexTagNames(Room $room)
    {
        $tags = Tag::query()
            ->whereHas('stampTags', function ($query) use ($room) {
                $query->where('room_id', $room->id);
            })
            ->orderBy('text')
            ->select('text')
            ->get();

        $tagNames = [];

        foreach ($tags as $tag) {
            $tagNames[] = $tag->text;
        }

        $result = [
            'tags' => $tagNames,
        ];

        return $result;
    }
}
