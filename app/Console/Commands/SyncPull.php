<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\v7\ConfigSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

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
        $this->makeSiteDownUp('down',true);

        //Stop primary logs
        $whereCondition = ['name' => 'PRIMARY_CHANGE_LOG', 'status' => 1];
        ConfigSetting::where($whereCondition)->update(['value' => '0']);

        $response = Http::withHeaders([
            'X-SYNC-TOKEN' => config('sync.api_token'),
        ])->post(config('sync.secondary.fetch_url'), []);

        if (! $response->successful()) {
            \Log::error('Failed to fetch logs');
            $this->makeSiteDownUp('up',false);
            return;
        }

        $changes = $response->json('changes', []);

        if (empty($changes))
        {
            //Start primary logs
            ConfigSetting::where($whereCondition)->update(['value' => '1']);

            // Make site up
            $this->makeSiteDownUp('up',false);

            \Log::info('No changes to apply');
            return;
        }

        Model::withoutEvents(function () use ($changes) {

            foreach ($changes as $log) {

                if (!Schema::hasTable($log['table_name']))  continue;
                
                DB::transaction(function () use ($log) {
                    match ($log['action']) {
                        'insert', 'update' =>
                            DB::table($log['table_name'])->updateOrInsert(
                                ['id' => $log['record_id']],
                                $log['payload'] ?? []
                            ),

                        'delete' =>
                            DB::table($log['table_name'])
                                ->where('id', $log['record_id'])
                                ->delete(),
                    };
                });
            }
        });

        $ids = collect($changes)->pluck('id')->toArray();

        Http::withHeaders([
            'X-SYNC-TOKEN' => config('sync.api_token'),
        ])->post(config('sync.secondary.secondary_mark_synced_url'), [
            'ids' => $ids,
        ]);

        \Log::info('Pull sync completed');
    }

    public function makeSiteDownUp($action,$check = false) {
        if($action=='up'){
            if($check){
                if (App::isDownForMaintenance()) Artisan::call('up');
            }else{
                Artisan::call('up');
            }
        }

        if($action=='down'){
            if($check){
                if (!App::isDownForMaintenance()) Artisan::call('down');
            }else{
                Artisan::call('down');
            }
        }
    }
}
