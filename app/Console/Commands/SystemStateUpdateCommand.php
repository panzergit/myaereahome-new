<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\v7\ConfigSetting;

class SystemStateUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aereahome:system_state_update';

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
            \Log::info('Primary is marked as down. Skipping state update.');
            return;
        }

        $upTime = now()->toDateTimeString();
        
        ConfigSetting::updateOrInsert(
            ['name' => 'PRIMARY_STATE'],
            ['value' => 'up', 'updated_at' => $upTime]
        );

        Http::withHeaders([
            'X-SYNC-TOKEN' => config('sync.api_token'),
        ])->post(config('sync.secondary.primary_state_url'), ['up_time' => $upTime]);

        \Log::info('Primary state updated.');
    }
}
