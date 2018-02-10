<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStamp;
use App\Http\Requests\StoreStampGuest;
use App\Imprint;
use App\Room;
use App\Stamp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StampController extends Controller
{
    /**
     * スタンプ一覧の取得
     *
     * @return array
     */
    public function index(Request $request, Room $room)
    {
        $stamps = [];

        /** @var User $roomOwner */
        $roomOwner = $room->user;

        $blackListIps = $roomOwner->blackListIps->pluck('ip');
        $blackListUserIds = $roomOwner->blackListUsers->pluck('id');

        // 無限スクロールで一部を取得するか、ページネートで全件取得するかはpageの有無で判断する
        if ($request->exists('page')) {
            // 無限スクロール

            $page = $request->get('page', 1);
            $offset = Room::STAMP_COUNT_PER_PAGE * ($page - 1);

            switch ($request->sort) {
                case 'latest':
                    // ルームに送信されたStampを新しい順に取得する
                    $imprints = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        }])
                        ->select('stamp_id')
                        ->latest()
                        ->get();

                    // 重複データを削除する
                    $imprints = $imprints->uniqueStrict('stamp_id')->values();

                    foreach ($imprints->splice($offset, Room::STAMP_COUNT_PER_PAGE) as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;
                case 'count':
                    // ルームに送信されたStampを件数順に取得する

                    /** @var Collection $imprints */
                    $imprints = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        }])
                        ->select('stamp_id')
                        ->groupBy('stamp_id')
                        ->orderBy(DB::raw('COUNT(stamp_id)'), 'desc')
                        ->orderBy('stamp_id', 'desc')
                        ->get();

                    // groupByを利用しているSQLで、limitとoffsetが利用できないので、Collection取得後のここで行う
                    foreach ($imprints->splice($offset, Room::STAMP_COUNT_PER_PAGE) as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;

                default:
                    // 現在表示中のRoomに紐付いたStampか、何のルームにも紐付いていないStampを取得する
                    $stamps = Stamp::query()
                        ->where(function ($query) use ($room) {
                            $query->where('room_id', $room->id)
                                ->orWhereNull('room_id');
                        })
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->limit(Room::STAMP_COUNT_PER_PAGE)
                        ->offset($offset)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->get();
                    break;
            }

        } else {
            // Pagination

            switch ($request->sort) {
                case 'latest':
                    // ルームに送信されたStampを新しい順に取得する
                    $imprints = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        }])
                        ->select('stamp_id')
                        ->latest()
                        ->get();

                    // 重複データを削除する
                    $imprints = $imprints->uniqueStrict('stamp_id')->values();

                    foreach ($imprints as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;
                case 'count':
                    // ルームに送信されたStampを件数順に取得する

                    /** @var Collection $imprints */
                    $imprints = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        }])
                        ->select('stamp_id')
                        ->groupBy('stamp_id')
                        ->orderBy(DB::raw('COUNT(stamp_id)'), 'desc')
                        ->orderBy('stamp_id', 'desc')
                        ->get();

                    foreach ($imprints as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;

                default:
                    // 現在表示中のRoomに紐付いたStampか、何のルームにも紐付いていないStampを取得する
                    $stamps = Stamp::query()
                        ->where(function ($query) use ($room) {
                            $query->where('room_id', $room->id)
                                ->orWhereNull('room_id');
                        })
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->get();
                    break;
            }
        }

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
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $stamp = new Stamp;
        $stamp->user_id = $request->user()->id; // ログインユーザーID
        $stamp->room_id = $room->id;
        $stamp->name = $file->store('stamps');
        $stamp->size = $file->getSize();
        $stamp->ip = $request->ip();
        $stamp->hash = md5_file($file->getRealPath());

        // 画像情報の取得
        $imageSize = getimagesize($file->getRealPath());
        $stamp->width = $imageSize[0];
        $stamp->height = $imageSize[1];
        $stamp->mime_type = $imageSize['mime'];

        $stamp->save();

        return [
            'stamp' => $stamp
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
        $stamp->ip = $request->ip();
        $stamp->hash = md5_file($file->getRealPath());

        // 画像情報の取得
        $imageSize = getimagesize($file->getRealPath());
        $stamp->width = $imageSize[0];
        $stamp->height = $imageSize[1];
        $stamp->mime_type = $imageSize['mime'];

        $stamp->save();

        return [
            'stamp' => $stamp
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

    public function uploadedIndex()
    {
        /** @var User $user */
        $user = auth()->user();

        $stamps = $user->stamps()->with('room')->get();

        return view('stamp.index', compact('stamps'));
    }

    public function uploadedDelete(Request $request, Stamp $stamp)
    {
        $user = $request->user();

        if (($stamp->user_id === $user->id) || ($stamp->room->user_id === $user->id)) {
            // 論理削除
            $stamp->deleted_at = Carbon::now();
            $stamp->save();
        } else {
            abort(403);
        }
    }
}
