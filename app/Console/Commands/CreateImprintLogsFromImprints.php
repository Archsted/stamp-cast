<?php

namespace App\Console\Commands;

use App\Imprint;
use App\ImprintLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateImprintLogsFromImprints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imprint_logs:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imprintsテーブルの情報からImprintLogsテーブルのレコードを作成';

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
        $imprints = Imprint::query()
            ->selectRaw('max(id) as id, count(*) cnt, room_id, stamp_id')
            ->groupBy(['room_id', 'stamp_id'])
            ->get();

        foreach ($imprints as $imprint) {
            ImprintLog::create([
                'imprint_id' => $imprint->id,
                'room_id' => $imprint->room_id,
                'stamp_id' => $imprint->stamp_id,
                'count' => $imprint->cnt,
            ]);
        }
    }
}
