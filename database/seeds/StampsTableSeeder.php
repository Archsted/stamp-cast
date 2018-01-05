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
            $stamp->name = Storage::putFile('stamps', new File($fullPath));
            $stamp->size = Storage::disk('seed')->size($fileName);

            // 画像情報の取得
            $imageSize = getimagesize($fullPath);
            $stamp->width = $imageSize[0];
            $stamp->height = $imageSize[1];
            $stamp->mime_type = $imageSize['mime'];

            $stamp->save();
        }
    }
}
