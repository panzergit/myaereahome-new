<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

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
        $lastKnown = DB::table('system_state')
            ->where('key_name', 'primary_status')
            ->value('updated_at');

        if (now()->diffInMinutes($lastKnown) > 3) Artisan::call('aereahome:sync_pull');
    }
}
