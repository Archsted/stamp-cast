<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStamp;
use App\Http\Requests\StoreStampGuest;
use App\Http\Requests\UpdateTags;
use App\Imprint;
use App\Room;
use App\Stamp;
use App\Tag;
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
        ini_set('memory_limit', '512M');

        $stamps = [];

        /** @var User $roomOwner */
        $roomOwner = $room->user;

        $blackListIps = $roomOwner->blackListIps->pluck('ip');
        $blackListUserIds = $roomOwner->blackListUsers->pluck('id');

        // お気に入りのみを表示するかどうか
        $onlyFavorite = $request->get('onlyFavorite', 0);

        // キーワード（タグ）
        $tag = $request->get('tag');

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
                        ->whereHas('stamp', function ($query) use ($blackListUserIds, $blackListIps, $tag) {
                            $query->withoutBlackList($blackListIps, $blackListUserIds);
                        })
                        ->with([
                            'stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                                $query
                                    ->withoutBlackList($blackListIps, $blackListUserIds)
                                    ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail', 'is_animation']);
                            },
                            'stamp.tags' => function ($query) use ($room) {
                                $query->where('room_id', $room->id);
                            }
                        ])
                        ->select('stamp_id')
                        ->latest();

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    // タグ指定があった場合
                    if (!is_null($tag)) {
                        $query->whereHas('stamp.tags', function ($query) use ($tag, $room) {
                           $query
                               ->where('text', $tag)
                               ->where('room_id', $room->id);
                        });
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
                        ->with([
                            'stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                                $query
                                    ->withoutBlackList($blackListIps, $blackListUserIds)
                                    ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail', 'is_animation']);
                            },
                            'stamp.tags' => function ($query) use ($room) {
                                $query->where('room_id', $room->id);
                            }
                        ])
                        ->select('stamp_id')
                        ->groupBy('stamp_id')
                        ->orderBy(DB::raw('COUNT(stamp_id)'), 'desc')
                        ->orderBy('stamp_id', 'desc');

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    // タグ指定があった場合
                    if (!is_null($tag)) {
                        $query->whereHas('stamp.tags', function ($query) use ($tag, $room) {
                            $query
                                ->where('text', $tag)
                                ->where('room_id', $room->id);
                        });
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
                        ->with([
                            'tags' => function ($query) use ($room) {
                                $query->where('room_id', $room->id);
                            }
                        ])
                        ->limit(Room::STAMP_COUNT_PER_PAGE)
                        ->offset($offset)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail', 'is_animation']);

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('id', $favoriteStampIds);
                    }

                    // タグ指定があった場合
                    if (!is_null($tag)) {
                        $query->whereHas('tags', function ($tagsQuery) use ($tag, $room) {
                            $tagsQuery
                                ->where('text', $tag)
                                ->where('room_id', $room->id);
                        });
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
                        ->with([
                            'stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                                $query
                                    ->withoutBlackList($blackListIps, $blackListUserIds)
                                    ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail', 'is_animation']);
                            },
                            'stamp.tags' => function ($query) use ($room) {
                                $query->where('room_id', $room->id);
                            }
                        ])
                        ->select('stamp_id')
                        ->latest();

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    // タグ指定があった場合
                    if (!is_null($tag)) {
                        $query->whereHas('stamp.tags', function ($query) use ($tag, $room) {
                            $query
                                ->where('text', $tag)
                                ->where('room_id', $room->id);
                        });
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
                        ->with([
                            'stamp' => function ($query) use ($blackListUserIds, $blackListIps) {
                                $query
                                    ->withoutBlackList($blackListIps, $blackListUserIds)
                                    ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail', 'is_animation']);
                            },
                            'stamp.tags' => function ($query) use ($room) {
                                $query->where('room_id', $room->id);
                            }
                        ])
                        ->select('stamp_id')
                        ->groupBy('stamp_id')
                        ->orderBy(DB::raw('COUNT(stamp_id)'), 'desc')
                        ->orderBy('stamp_id', 'desc');

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('stamp_id', $favoriteStampIds);
                    }

                    // タグ指定があった場合
                    if (!is_null($tag)) {
                        $query->whereHas('stamp.tags', function ($query) use ($tag, $room) {
                            $query
                                ->where('text', $tag)
                                ->where('room_id', $room->id);
                        });
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
                        ->with([
                            'tags' => function ($query) use ($room) {
                                $query->where('room_id', $room->id);
                            }
                        ])
                        ->select(['id', 'user_id', 'room_id', 'name', 'thumbnail', 'is_animation'])
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc');

                    // お気に入りのみを取得する場合
                    if ($onlyFavorite && $requestUser) {
                        $query->whereIn('id', $favoriteStampIds);
                    }

                    // タグ指定があった場合
                    if (!is_null($tag)) {
                        $query->whereHas('tags', function ($query) use ($tag, $room) {
                            $query
                                ->where('text', $tag)
                                ->where('room_id', $room->id);
                        });
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

        // サムネイルの作成を行う。アニメgifかそれ以外
        if ($stamp->mime_type === 'image/gif') {
            try {
                // アニメgif
                $image = new \Imagick();

                $image->readImage($file->getRealPath());
                $frameCount = $image->getNumberImages();

                // 1フレーム目のみを使って静止画のサムネイルを作成する
                $image->setFirstIterator();
                $image = $image->getImage();

                if ($stamp->height > env('THUMBNAIL_HEIGHT')) {
                    // 横幅をオート（null）
                    $image->adaptiveResizeImage(null, env('THUMBNAIL_HEIGHT'));
                }

                $image->writeImage($thumbnailFullPath);

                $image->clear();

                // アニメーションがついていた場合（フレーム数が2枚以上あった場合）
                if ($frameCount > 1) {
                    // アニメーションフラグをセット
                    $stamp->is_animation = 1;
                }
            } catch (\Exception $e) {
                abort(500, 'アニメgifのリサイズに失敗しました。');
            }
        } else {
            $this->createThumbnail($file, $stamp, $thumbnailFullPath);
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
                'is_animation' => $stamp->is_animation,
                'tags' => [], // 作成直後にタグはまだ無いため
            ]
        ];
    }

    /**
     * サムネイル作成処理
     *
     * @param UploadedFile $file
     * @param $stamp
     * @param $savePath
     */
    private function createThumbnail(UploadedFile $file, $stamp, $savePath)
    {
        // 通常の画像
        $img = Image::make($file);

        // 高さがリサイズ後の高さを超える場合
        if ($stamp->height > env('THUMBNAIL_HEIGHT')) {
            // アスペクト比を保ったままリサイズ
            $img->resize(null, env('THUMBNAIL_HEIGHT'), function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        // サムネイル画像の出力
        $img->save($savePath, 20);
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
     * 対象のスタンプに紐づくタグ名の配列を返す
     *
     * @param Room $room
     * @param Stamp $stamp
     * @return array
     */
    public function indexTags(Room $room, Stamp $stamp)
    {
        $tags = $stamp->tags()
            ->wherePivot('room_id', $room->id)
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

    /**
     * タグの修正を行う
     *
     * @param UpdateTags $request
     * @param Room $room
     * @param Stamp $stamp
     * @return array
     */
    public function updateTags(UpdateTags $request, Room $room, Stamp $stamp)
    {
        // 入力したタグ文字列の配列
        $tagNames = $request->get('tags');

        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrNew(['text' => $tagName]);

            // 新規レコードの場合、IPをセットして保存
            if (!$tag->exists) {
                $tag->ip = $request->ip();
                $tag->save();
            }

            $tagIds[$tag->id] = [
                'ip' => $request->ip(),
                'room_id' => $room->id,
            ];
        }

        // 保存
        $stamp->tags()->wherePivot('room_id', $room->id)->sync($tagIds);

        $result = [
            'tags' => $stamp->tags()->wherePivot('room_id', $room->id)->get()
        ];

        return $result;
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
