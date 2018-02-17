<?php

namespace App\Console\Commands;

use App\Stamp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbnail:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'サムネイルファイルを削除し、DBのthumbnailの値をnullにする。';

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
        Stamp::query()
            ->where('mime_type', 'image/gif')
            ->whereNotNull('thumbnail')
            ->chunk(200, function ($stamps) {
                foreach ($stamps as $stamp) {
                    $thumbnailName = $stamp->getOriginal('thumbnail');

                    if (Storage::exists($thumbnailName) ) {
                        Storage::delete($thumbnailName);
                    }

                    $stamp->thumbnail = null;
                    $stamp->save();
                }
            });
    }
}
