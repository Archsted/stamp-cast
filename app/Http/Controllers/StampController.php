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
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

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

        // お気に入りのみを表示するかどうか
        $onlyFavorite = $request->get('onlyFavorite', 0);

        $requestUser = $request->user();
        if ($requestUser) {
            $favoriteStampIds = $requestUser->favorites->pluck('id')->toArray();
        } else {
            $favoriteStampIds = [];
        }

        // 無限スクロールで一部を取得するか、ページネートで全件取得するかはpageの有無で判断する
        if ($request->exists('page')) {
            // 無限スクロール

            $page = $request->get('page', 1);
            $offset = Room::STAMP_COUNT_PER_PAGE * ($page - 1);

            switch ($request->sort) {
                case 'latest':
                    // ルームに送信されたStampを新しい順に取得する
                    $query = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds)
                                ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail']);
                        }])
                        ->select('stamp_id')
                        ->latest();

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    $imprints = $query->get();

                    // 重複データを削除する
                    $imprints = $imprints->uniqueStrict('stamp_id')->values();

                    foreach ($imprints->splice($offset, Room::STAMP_COUNT_PER_PAGE) as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;
                case 'count':
                    // ルームに送信されたStampを件数順に取得する

                    /** @var Collection $imprints */
                    $query = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds)
                                ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail']);
                        }])
                        ->select('stamp_id')
                        ->groupBy('stamp_id')
                        ->orderBy(DB::raw('COUNT(stamp_id)'), 'desc')
                        ->orderBy('stamp_id', 'desc');

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    $imprints = $query->get();

                    // groupByを利用しているSQLで、limitとoffsetが利用できないので、Collection取得後のここで行う
                    foreach ($imprints->splice($offset, Room::STAMP_COUNT_PER_PAGE) as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;

                default:
                    // 現在表示中のRoomに紐付いたStampか、何のルームにも紐付いていないStampを取得する
                    $query = Stamp::query()
                        ->where(function ($query) use ($room) {
                            $query->where('room_id', $room->id)
                                ->orWhereNull('room_id');
                        })
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->limit(Room::STAMP_COUNT_PER_PAGE)
                        ->offset($offset)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail']);

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('id', $favoriteStampIds);
                    }

                    $stamps = $query->get();

                    break;
            }

        } else {
            // Pagination

            switch ($request->sort) {
                case 'latest':
                    // ルームに送信されたStampを新しい順に取得する
                    $query = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds)
                                ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail']);
                        }])
                        ->select('stamp_id')
                        ->latest();

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    $imprints = $query->get();

                    // 重複データを削除する
                    $imprints = $imprints->uniqueStrict('stamp_id')->values();

                    foreach ($imprints as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;
                case 'count':
                    // ルームに送信されたStampを件数順に取得する

                    /** @var Collection $imprints */
                    $query = Imprint::query()
                        ->where('room_id', $room->id)
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with(['stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds)
                                ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail']);
                        }])
                        ->select('stamp_id')
                        ->groupBy('stamp_id')
                        ->orderBy(DB::raw('COUNT(stamp_id)'), 'desc')
                        ->orderBy('stamp_id', 'desc');

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    $imprints = $query->get();

                    foreach ($imprints as $imprint) {
                        $stamps[] = $imprint->stamp;
                    }

                    break;

                default:
                    // 現在表示中のRoomに紐付いたStampか、何のルームにも紐付いていないStampを取得する
                    $query = Stamp::query()
                        ->where(function ($query) use ($room) {
                            $query->where('room_id', $room->id)
                                ->orWhereNull('room_id');
                        })
                        ->withoutBlackList($blackListIps, $blackListUserIds)
                        ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail'])
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc');

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('id', $favoriteStampIds);
                    }

                    $stamps = $query->get();

                    break;
            }
        }

        return [
            'stamps' => $stamps
        ];
    }

    /**
     * スタンプの登録
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
        $stamp->room_id = $room->id;

        // アップロードファイルの保存とファイル名を取得
        $stampPath = $file->store('stamps');

        // サムネイルのストレージルート以降のファイル名
        $thumbnailName = preg_replace('/^stamps/', 'thumbnails', $stampPath);

        // デフォルトストレージのパスを取得する
        $storagePath  = Storage::getDriver()->getAdapter()->getPathPrefix();

        // サムネイルのフルパス
        $thumbnailFullPath = $storagePath . $thumbnailName;

        $stamp->name = $stampPath; // ここはストレージルート以降のファイル名
        $stamp->size = $file->getSize();
        $stamp->ip = $request->ip();
        $stamp->hash = md5_file($file->getRealPath());

        // ログイン中ユーザ情報が取得できたら
        if ($request->user()) {
            $stamp->user_id = $request->user()->id; // ログインユーザーID
        }

        // 画像情報の取得
        $imageSize = getimagesize($file->getRealPath());
        $stamp->width = $imageSize[0];
        $stamp->height = $imageSize[1];
        $stamp->mime_type = $imageSize['mime'];

        // リサイズ後のMAX高さ
        $maxHeight = 140;

        // サムネイルの作成を行う。アニメgifかそれ以外
        if ($stamp->mime_type === 'image/gif') {
            try {
                // アニメgif
                $image = new \Imagick();

                $image->readImage($file->getRealPath());
                $image->setFirstIterator();
                $image = $image->coalesceImages();

                do {
                    if ($stamp->height > $maxHeight) {
                            // 横幅をオート（null）
                            $image->adaptiveResizeImage(null, $maxHeight);
                    }
                } while ($image->nextImage());

                $image = $image->optimizeImageLayers();

                $image->writeImages($thumbnailFullPath, true);

                $image->clear();
            } catch (\Exception $e) {
                abort(500, 'アニメgifのリサイズに失敗しました。');
            }
        } else {
            // 通常の画像
            $img = Image::make($file);

            // 高さがリサイズ後の高さを超える場合
            if ($stamp->height > $maxHeight) {
                // アスペクト比を保ったままリサイズ
                $img->resize(null, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            // サムネイル画像の出力
            $img->save($thumbnailFullPath, 20);
        }

        // サムネイルのパスをスタンプテーブルに設定
        $stamp->thumbnail = $thumbnailName;

        // スタンプをDBに保存
        $stamp->save();

        return [
            'stamp' => [
                'id' => $stamp->id,
                'user_id' => $stamp->user_id,
                'room_id' => $stamp->room_id,
                'name' => $stamp->name,
                'thumbnail' => $stamp->thumbnail,
            ]
        ];
    }

    /**
     * トップページ用の共有スタンプを取得
     *
     * @return array
     */
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

    /**
     * スタンプの削除
     *
     * @param Request $request
     * @param Stamp $stamp
     */
    public function uploadedDelete(Request $request, Stamp $stamp)
    {
        $user = $request->user();

        if (($stamp->user_id === $user->id) || ($stamp->room->user_id === $user->id)) {
            // 論理削除
            $stamp->deleted_at = Carbon::now();
            $stamp->save();

            $this->deleteImage($stamp);
        } else {
            abort(403);
        }
    }

    /**
     * スタンプ画像を削除する
     *
     * @param Stamp $stamp
     */
    private function deleteImage(Stamp $stamp)
    {
        // オリジナルのファイル名を取り出す
        $originalName = $stamp->getOriginal('name');

        // サムネイルファイル名
        $thumbnailName = preg_replace('/^stamps/', 'thumbnails', $originalName);

        // ファイルが存在したら削除する
        if (Storage::exists($originalName)) {
            Storage::delete($originalName);
        }

        // サムネイルが存在したら削除する
        if (Storage::exists($thumbnailName)) {
            Storage::delete($thumbnailName);
        }
    }
}
