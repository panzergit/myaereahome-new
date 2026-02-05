<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncPull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aereahome:sync_pull';

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
        $response = Http::withHeaders([
            'X-SYNC-TOKEN' => config('sync.api_token'),
        ])->post(config('sync.secondary.fetch_url'), []);

        if (! $response->successful()) {
            \Log::error('Failed to fetch logs');
            return;
        }

        foreach ($response->json() as $log) {

            DB::transaction(function () use ($log) {

                match ($log['action']) {
                    'insert' => DB::table($log['table_name'])
                        ->insert((array) $log['payload']),

                    'update' => DB::table($log['table_name'])
                        ->where('id', $log['record_id'])
                        ->update((array) $log['payload']),

                    'delete' => DB::table($log['table_name'])
                        ->where('id', $log['record_id'])
                        ->delete(),
                };

                DB::table('change_logs')->updateOrInsert(
                    ['id' => $log['id']],
                    array_merge($log, ['synced' => 1])
                );
            });
        }

        \Log::info('Pull sync completed');
    }
}
