<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\v7\ChangeLog;

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
        if(!is_primary_serv_active()) {
            \Log::info('Primary is down. Skipping sync push.');
            return;
        }

        if(empty(config('sync.secondary.sync_url'))) {
            $this->error('Secondary sync URL not configured');
            return;
        }

        $logs = ChangeLog::where(['synced' => 0, 'server_id' => config('sync.server_id'), 'status' => 1])
            ->orderBy('id')
            ->limit(100)->get();

        if ($logs->isEmpty()) {
            \Log::info('No changes to sync');
            return;
        }

        $response = Http::withHeaders([
            'X-SYNC-TOKEN' => config('sync.api_token'),
        ])->post(config('sync.secondary.sync_url'), ['changes' => $logs->toArray()]);

        if ($response->successful()) ChangeLog::whereIn('id', $logs->pluck('id'))->update(['synced' => 1]);

        \Log::info('Push sync completed');
    }
}