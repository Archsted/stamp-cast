<?php

namespace App\Console\Commands;

use App\Stamp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CreateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbnail:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'スタンプのサムネイルを作成';

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
        $count = Stamp::query()
            ->where('mime_type', 'image/gif')
            ->count();

        $bar = $this->output->createProgressBar($count);

        Stamp::query()
            ->where('mime_type', 'image/gif')
            ->orderBy('id')
            ->chunk(200, function ($stamps) use ($bar) {
                foreach ($stamps as $stamp) {
                    $this->createThumbnail($stamp);
                    $bar->advance();
                }
            });

        $bar->finish();

        echo PHP_EOL;
    }

    private function createThumbnail(Stamp $stamp)
    {
        // リサイズ後のMAX高さ
        $maxHeight = env('THUMBNAIL_HEIGHT');

        // アップロードファイルの保存とファイル名を取得
        $stampPath = $stamp->getOriginal('name');

        // サムネイルのストレージルート以降のファイル名
        $thumbnailName = preg_replace('/^stamps/', 'thumbnails', $stampPath);

        // デフォルトストレージのパスを取得する
        $storagePath  = Storage::getDriver()->getAdapter()->getPathPrefix();

        // サムネイルのフルパス
        $thumbnailFullPath = $storagePath . $thumbnailName;

        // オリジナルファイルのフルパス
        $originalFullPath = $storagePath . $stampPath;

        // 初期設定
        $stamp->is_animation = 0;

        // サムネイルの作成を行う。アニメgifかそれ以外
        if ($stamp->mime_type === 'image/gif') {
            try {
                // アニメgif
                $image = new \Imagick();

                $image->readImage($originalFullPath);
                $frameCount = $image->getNumberImages();
                // 1フレーム目のみを使って静止画のサムネイルを作成する

                $image->setFirstIterator();
                $image->nextImage();
                if ($stamp->height > env('THUMBNAIL_HEIGHT')) {
                    // 横幅をオート（null）
                    $image->adaptiveResizeImage(null, env('THUMBNAIL_HEIGHT'));
                }

                $image->writeImage($thumbnailFullPath);

                $image->clear();

                // アニメーションフラグをセット
                if ($frameCount > 1) {
                    $stamp->is_animation = 1;
                }

            } catch (\Exception $e) {
                abort(500, 'アニメgifのリサイズに失敗しました。');
            }
        } else {
            // 通常の画像
            $img = Image::make($originalFullPath);

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
    }
}
