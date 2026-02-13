<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\v7\ConfigSetting;

class CheckPrimaryActiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aereahome:check_primary_active';

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
        $lastKnown = ConfigSetting::where(['name' => 'PRIMARY_STATE', 'status' => 1])->value('updated_at');
        
        if (!empty($lastKnown) && now()->diffInMinutes($lastKnown) > 3) Artisan::call('aereahome:sync_pull');
    }
}
