<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\v7\ChangeLog;

class ClearChangeLogs extends Command
{
    protected $signature = 'aereahome:clear_change_logs';

    protected $description = 'Delete entries older than 2 month';

    public function handle(): int
    {
        $date = Carbon::now()->subMonth();
        $totalDeleted = 0;

        ChangeLog::where([['created_at', '<', $date],['status', '=', 1],['synced', '=', 1]])
            ->chunkById(1000, function ($records) use (&$totalDeleted) {
                $ids = $records->pluck('id');
                $deleted = ChangeLog::whereIn('id', $ids)->delete();
                $totalDeleted += $deleted;
            });

        \Log::info("Deleted {$totalDeleted} old records.");

        return Command::SUCCESS;
    }
}
