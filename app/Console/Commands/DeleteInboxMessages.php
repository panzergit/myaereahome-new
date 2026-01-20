<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Model\v7\InboxMessage;

class DeleteInboxMessages extends Command
{
    protected $signature = 'aereahome:delete_inbox_messages';

    protected $description = 'Delete entries older than 6 months';

    public function handle()
    {
        $date = Carbon::now()->subMonthsNoOverflow(6);
        $deleted = InboxMessage::where('created_at', '<', $date)->delete();
        $this->info("Deleted {$deleted} old records.");
    }
}
