<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\v7\ConfigSetting;
class CheckPrimaryDownCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aereahome:check_primary_down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Request $request)
    {
        $domain = $request->getHost();

        if ($domain === 'aereanew.panzerplayground.com')
        {
            // Fecth last updated primary sync time
            $last = ConfigSetting::where(['name' => 'PRIMARY_STATE', 'status' => 1])->value('updated_at') === '1';
            
            // Enable secondary change log if primary sync time more than 3 min
            // Disable secondary change log if primary sync time more less than 3 min
            ConfigSetting::updateOrInsert(
                ['name' => 'SECONDARY_CHANGE_LOG', 'status' => 1],
                ['value' => (now()->diffInMinutes($last ?? now()) > 3 ? '1' : '0'), 'updated_at' => now()]
            ); 
        }
    }
}
