<?php

use App\Stamp;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class StampsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seedDir = database_path('seeds/');

        $fileNames = Storage::disk('seed')->files('images');

        foreach ($fileNames as $fileName) {
            $fullPath = $seedDir . $fileName;

            $stamp = new Stamp;

            $stamp->room_id = null; // ルーム共有

            $stampPath = Storage::putFile('stamps', new File($fullPath));

            // サムネイルのストレージルート以降のファイル名
            $thumbnailName = preg_replace('/^stamps/', 'thumbnails', $stampPath);

            // デフォルトストレージのパスを取得する
            $storagePath  = Storage::getDriver()->getAdapter()->getPathPrefix();

            // サムネイルのフルパス
            $thumbnailFullPath = $storagePath . $thumbnailName;

            $stamp->name = $stampPath;
            $stamp->size = Storage::disk('seed')->size($fileName);

            $stamp->ip = null;
            $stamp->hash = md5_file($fullPath);

            // 画像情報の取得
            $imageSize = getimagesize($fullPath);
            $stamp->width = $imageSize[0];
            $stamp->height = $imageSize[1];
            $stamp->mime_type = $imageSize['mime'];

            $stamp->save();

            // サムネイルの作成を行う。アニメgifかそれ以外
            if ($stamp->mime_type === 'image/gif') {
                try {
                    // アニメgif
                    $image = new \Imagick();

                    $image->readImage($fullPath);
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
                // 通常の画像
                $img = \Intervention\Image\Facades\Image::make($fullPath);

                // 高さがリサイズ後の高さを超える場合
                if ($stamp->height > env('THUMBNAIL_HEIGHT')) {
                    // アスペクト比を保ったままリサイズ
                    $img->resize(null, env('THUMBNAIL_HEIGHT'), function ($constraint) {
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
}
