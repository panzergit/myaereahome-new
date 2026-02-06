<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

        \Log::info("Checking primary status for domain: $domain");
        
        if ($domain === 'aereanew.panzerplayground.com')
        {
            $last = DB::table('system_state')
                ->where('key_name', 'primary_status')
                ->value('updated_at');

            if (now()->diffInMinutes($last) > 3) config(['sync.secondary.disable_logging' => false]);
        }
    }
}
