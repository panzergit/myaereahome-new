<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ChangeLogService
{
    /**
     * Log insert / update / delete changes
     */
    public static function log(string $action, Model $model): void
    {
        // Disable logging during sync replay
        if (config('sync.disable_logging', false)) {
            return;
        }

        // Ignore specific tables
        if (in_array($model->getTable(), config('sync.ignore_tables', []))) {
            return;
        }

        // Prepare payload
        $payload = match ($action) {
            'delete' => null,
            default  => self::sanitize($model),
        };

        \Log::info("Change log inserted: {$action} on {$model->getTable()} ID {$model->getKey()}");
        // Insert change log
        DB::table('change_logs')->insert([
            'server_id'  => config('sync.server_id'),
            'table_name' => $model->getTable(),
            'record_id'  => $model->getKey(),
            'action'     => $action,
            'payload'    => $payload ? json_encode($payload) : null,
            'created_at' => now(),
        ]);
    }

    /**
     * Remove sensitive / unwanted fields
     */
    protected static function sanitize(Model $model): array
    {
        return collect($model->getAttributes())
            ->except([
                'password',
                'remember_token',
                'created_at',
                'updated_at',
            ])
            ->toArray();
    }
}
