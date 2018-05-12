<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\CopyBookStamps;
use App\Http\Requests\DestroyBookStamps;
use App\Http\Requests\MoveBookStamps;
use App\Http\Requests\StoreBook;
use App\Http\Requests\StoreBookStamp;
use App\Http\Requests\UpdateBook;
use App\Http\Requests\UpdateBookOrder;
use App\Http\Requests\UpdateBookStampOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * 一覧画面の表示
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $books = request()
            ->user()
            ->books()
            ->withCount('stamps')
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->get();

        return view('book.index', compact('books'));
    }

    /**
     * 新規作成画面を表示
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('book.create');
    }

    /**
     * 新規登録処理
     *
     * @param StoreBook $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(StoreBook $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->only(['name', 'description']);
            $data['user_id'] = $request->user()->id;

            // 追加前に、存在するスタンプ帳のソート値を+1する
            DB::table('books')
                ->where('user_id', $request->user()->id)
                ->increment('order');

            Book::create($data);
        });

        return redirect()->route('book_index');
    }

    /**
     * 詳細画面を表示
     *
     * @param Book $book
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Book $book)
    {
        return view('book.show', compact('book'));
    }

    /**
     * 編集画面を表示
     *
     * @param Book $book
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Book $book)
    {
        return view('book.edit', compact('book'));
    }

    /**
     * 編集処理
     *
     * @param Book $book
     * @param UpdateBook $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Book $book, UpdateBook $request)
    {
        $data = $request->only(['name', 'description']);

        $book->update($data);

        return redirect()->route('book_index');
    }

    /**
     * API 一覧取得
     * 関連スタンプはIDのみ
     *
     * @return mixed
     */
    public function indexApi()
    {
        $books = request()->user()->books()->orderBy('order')->orderBy('id', 'desc');

        $books->with(['stamps' => function ($sql) {
            $sql->select('stamps.id');
        }]);

        return $books->get();
    }

    /**
     * API 詳細取得
     *
     * @param Book $book
     * @return Book
     */
    public function showApi(Book $book)
    {
        $book->load(['stamps' => function ($query) {
            $query->select([
                'stamps.id',
                'stamps.user_id',
                'stamps.room_id',
                'stamps.name',
                'stamps.thumbnail',
                'stamps.is_animation'
            ])
                ->orderBy('pivot_order');
        }]);

        return $book;
    }

    /**
     * API 新規登録処理
     *
     * @param Book $book
     * @param StoreBookStamp $request
     * @throws \Throwable
     */
    public function storeStampApi(Book $book, StoreBookStamp $request)
    {
        DB::transaction(function () use ($book, $request) {
            // 現在紐付いているスタンプのソート値を+1する
            DB::table('book_stamp')
                ->where('book_id', $book->id)
                ->increment('order');

            // 新規スタンプ紐付け
            $book->stamps()->syncWithoutDetaching($request->stamp_id);
        });
    }

    /**
     * API スタンプ帳の並び順更新処理
     *
     * @param Book $book
     * @param UpdateBookOrder $request
     * @throws \Throwable
     */
    public function updateOrderApi(Book $book, UpdateBookOrder $request)
    {
        // RequestでユーザIDのチェックを行っている

        DB::transaction(function () use ($book, $request) {
            $maxCount = $request->user()->books()->count();

            $newOrder = min($maxCount, $request->order);
            $oldOrder = $book->order;

            $book->order = $newOrder;
            $book->save();

            if ($newOrder > $oldOrder) {
                // oldOrder ~ newOrderを下げる
                DB::table('books')
                    ->where('id', '<>', $book->id)
                    ->where('user_id', $request->user()->id)
                    ->whereBetween('order', [$oldOrder, $newOrder])
                    ->decrement('order');

            } else {
                // newOrder ~ oldOrderを上げる
                DB::table('books')
                    ->where('id', '<>', $book->id)
                    ->where('user_id', $request->user()->id)
                    ->whereBetween('order', [$newOrder, $oldOrder])
                    ->increment('order');
            }
        });
    }

    /**
     * API スタンプ帳に登録されたスタンプの並び順更新処理
     *
     * @param Book $book
     * @param UpdateBookStampOrder $request
     * @throws \Throwable
     */
    public function updateStampOrderApi(Book $book, $stampId, UpdateBookStampOrder $request)
    {
        DB::transaction(function () use ($book, $stampId, $request) {
            $maxCount = $book->stamps()->count();

            $newOrder = min($maxCount, $request->order);
            $stamp = $book->stamps()->where('stamps.id', $stampId)->first();
            $oldOrder = $stamp->pivot->order;

            $book->stamps()->updateExistingPivot($stampId, ['order' => $newOrder]);

            if ($newOrder > $oldOrder) {
                // oldOrder ~ newOrderを下げる
                DB::table('book_stamp')
                    ->where('book_id', $book->id)
                    ->where('stamp_id', '<>', $stampId)
                    ->whereBetween('order', [$oldOrder, $newOrder])
                    ->decrement('order');

            } else {
                // newOrder ~ oldOrderを上げる
                DB::table('book_stamp')
                    ->where('book_id', $book->id)
                    ->where('stamp_id', '<>', $stampId)
                    ->whereBetween('order', [$newOrder, $oldOrder])
                    ->increment('order');
            }
        });
    }

    /**
     * API スタンプ帳の削除処理
     *
     * @param Book $book
     * @throws \Throwable
     */
    public function destroyApi(Book $book)
    {
        if ($book->user_id !== request()->user()->id) {
            abort(403, '他人のスタンプ帳は削除できません。');
        }

        DB::transaction(function () use ($book) {
            // 削除前にソート順番を保持しておく
            $order = $book->order;

            // 外部キー制約に引っかかるので、紐付いているスタンプを先に切り離す
            $book->stamps()->detach();

            // 削除
            $book->delete();

            // 削除したソート値よりも大きいもののソート値を-1する
            DB::table('books')
                ->where('user_id', request()->user()->id)
                ->where('order', '>', $order)
                ->decrement('order');
        });
    }

    /**
     * API スタンプ帳に登録されたスタンプの一括削除処理
     *
     * @param Book $book
     * @param DestroyBookStamps $request
     * @throws \Throwable
     */
    public function destroyStampsApi(Book $book, DestroyBookStamps $request)
    {
        DB::transaction(function () use ($book, $request) {
            // RequestでIDなどのチェックは行っている

            $stampIds = $request->get('stampIds', []);

            $stampIds = array_values($stampIds);

            foreach ($stampIds as $stampId) {
                $stamp = $book->stamps()->where('stamps.id', $stampId)->first();

                $book->stamps()->detach($stampId);

                // 現在紐付いているスタンプのソート値を+1する
                DB::table('book_stamp')
                    ->where('book_id', $book->id)
                    ->where('order', '>', $stamp->pivot->order)
                    ->decrement('order');
            }
        });
    }

    /**
     * API スタンプ帳に登録されたスタンプを別のスタンプ帳に移動する処理
     * 移動なので処理元からは削除する
     *
     * @param Book $book
     * @param MoveBookStamps $request
     * @throws \Throwable
     */
    public function moveStampsApi(Book $book, MoveBookStamps $request)
    {
        // RequestでIDなどのチェックは行っている
        DB::transaction(function () use ($book, $request) {
            $stampIds = $request->get('stampIds', []);

            $stampIds = array_values($stampIds);

            $targetBookId = $request->get('bookId');

            if ($targetBookId === 0) {
                // 既に存在しているbookのソート値を+1する
                DB::table('books')
                    ->where('user_id', $request->user()->id)
                    ->increment('order');

                // bookを新規作成
                $targetBook = Book::create([
                    'user_id' => $request->user()->id,
                    'name' => '',
                    'description' => '',
                ]);

                // 自身のidを使って名称を更新
                $targetBook->name = 'スタンプ帳: [id: ' . $targetBook->id . ']';
                $targetBook->save();
            } else {
                /** @var Book $targetBook */
                $targetBook = Book::findOrFail($targetBookId);
            }

            // 処理対象のBookにスタンプをコピー
            // 重複分のレコードは無視するように、attachではなくsyncWithoutDetachingを利用
            $changes = $targetBook->stamps()->syncWithoutDetaching($stampIds);

            $attachedCount = count($changes['attached']);

            if ($attachedCount > 0) {
                // もともと紐付いていたスタンプのorder値を増やす。
                DB::table('book_stamp')
                    ->whereNotIn('stamp_id', $changes['attached'])
                    ->where('book_id', $targetBook->id)
                    ->where('order', '>', 0)
                    ->increment('order', $attachedCount);

                // 今の処理で追加したものについて、orderを更新する
                foreach ($changes['attached'] as $index => $stampId) {
                    $targetBook->stamps()->updateExistingPivot($stampId, ['order' => $index + 1]);
                }
            }

            foreach ($stampIds as $stampId) {
                $stamp = $book->stamps()->where('stamps.id', $stampId)->first();

                $book->stamps()->detach($stampId);

                DB::table('book_stamp')
                    ->where('book_id', $book->id)
                    ->where('order', '>', $stamp->pivot->order)
                    ->decrement('order');
            }
        });
    }

    /**
     * API スタンプ帳に登録されたスタンプを別のスタンプ帳にコピーする処理
     * コピーなので処理元からは削除しない
     *
     * @param Book $book
     * @param CopyBookStamps $request
     * @throws \Throwable
     */
    public function copyStampsApi(Book $book, CopyBookStamps $request)
    {
        // RequestでIDなどのチェックは行っている
        DB::transaction(function () use ($book, $request) {
            $stampIds = $request->get('stampIds', []);

            $stampIds = array_values($stampIds);

            $targetBookId = $request->get('bookId');

            if ($targetBookId === 0) {
                // 既に存在しているbookのソート値を+1する
                DB::table('books')
                    ->where('user_id', $request->user()->id)
                    ->increment('order');

                // bookを新規作成
                $targetBook = Book::create([
                    'user_id' => $request->user()->id,
                    'name' => '',
                    'description' => '',
                ]);

                // 自身のidを使って名称を更新
                $targetBook->name = 'スタンプ帳: [id: ' . $targetBook->id . ']';
                $targetBook->save();
            } else {
                /** @var Book $targetBook */
                $targetBook = Book::findOrFail($targetBookId);
            }

            // 処理対象のBookにスタンプをコピー
            // 重複分のレコードは無視するように、attachではなくsyncWithoutDetachingを利用
            $changes = $targetBook->stamps()->syncWithoutDetaching($stampIds);

            $attachedCount = count($changes['attached']);

            if ($attachedCount > 0) {
                // もともと紐付いていたスタンプのorder値を増やす。
                DB::table('book_stamp')
                    ->whereNotIn('stamp_id', $changes['attached'])
                    ->where('book_id', $targetBook->id)
                    ->where('order', '>', 0)
                    ->increment('order', $attachedCount);

                // 今の処理で追加したものについて、orderを更新する
                foreach ($changes['attached'] as $index => $stampId) {
                    $targetBook->stamps()->updateExistingPivot($stampId, ['order' => $index + 1]);
                }
            }
        });
    }
}
