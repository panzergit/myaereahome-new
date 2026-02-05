<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aereahome:sync_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logs = DB::table('change_logs')
            ->where(['synced' => 0, 'server_id' => config('sync.server_id')])
            ->orderBy('id')
            ->limit(100)->get();

        if ($logs->isEmpty()) {
            $this->info('No changes to sync');
            return;
        }

        if(empty(config('services.secondary.sync_url'))) {
            $this->error('Secondary sync URL not configured');
            return;
        }

        foreach ($logs as $log) {
            $response = Http::withHeaders([
                'X-SYNC-TOKEN' => config('sync.api_token'),
            ])->post(config('services.secondary.sync_url'), ['log' => $log]);

            if ($response->successful()) DB::table('change_logs')->where('id', $log->id)
                ->update(['synced' => 1]);
        }

        $this->info('Push sync completed');
    }
}