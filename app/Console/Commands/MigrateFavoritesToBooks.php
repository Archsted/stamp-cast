<?php

namespace App\Console\Commands;

use App\Book;
use App\Favorites;
use Illuminate\Console\Command;

class MigrateFavoritesToBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'お気に入りからスタンプ帳にデータを移行する。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allFavorites = Favorites::query()
            ->whereHas('stamp', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['stamp' => function ($query) {
                $query->select([
                    'id',
                    'hash',
                ]);
            }])
            ->orderBy('user_id')
            ->get();

        // お気に入りデータをユーザーごとにまとめる
        $favoritesByUsers = $allFavorites->groupBy('user_id');

        foreach ($favoritesByUsers as $userId => $favorites) {

            $favoritesByHash = $favorites->groupBy('stamp.hash')->sortBy('id');

            $stampIds = [];

            foreach ($favoritesByHash as $groupByHash) {
                // hashでグループ化したもののうち、1つ目だけを使う
                $stampIds[] = $groupByHash[0]['stamp_id'];
            }

            /** @var Book $book */
            $book = Book::create([
                'user_id' => $userId,
                'name' => 'お気に入りからの移行分',
                'description' => 'スタンプ帳機能実装に伴い、お気に入りから移行したものです。',
            ]);

            foreach ($stampIds as $index => $stampId) {
                $book->stamps()->attach($stampId, ['order' => $index + 1]);
            }
        }
    }
}
