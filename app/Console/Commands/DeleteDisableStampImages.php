<?php

namespace App\Console\Commands;

use App\Stamp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteDisableStampImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '掃除用スクリプト。論理削除されたスタンプの画像が残っていた場合は削除する。';

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
        $stamps = Stamp::withoutGlobalScope('softDelete')
            ->whereNotNull('deleted_at')
            ->get();

        $bar = $this->output->createProgressBar(count($stamps));

        foreach ($stamps as $stamp) {
            $this->deleteImage($stamp);

            $bar->advance();
        }

        $bar->finish();

        echo PHP_EOL;
    }

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
