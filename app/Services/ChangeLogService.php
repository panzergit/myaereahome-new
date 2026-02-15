<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\v7\ChangeLog;
use App\Models\v7\ConfigSetting;

class ChangeLogService
{
    /**
     * Log insert / update / delete changes
     */
    public static function log(string $action, Model $model): void
    {
        // Ignore specific tables
        if (in_array($model->getTable(), config('sync.ignore_tables', []))) return;

        // Disable logging during sync replay
        if(config('sync.server_id') === 'primary')
        {
            $isLogEnabled = ConfigSetting::where(['name' => 'PRIMARY_CHANGE_LOG', 'status' => 1])->value('value');
            if (empty($isLogEnabled) || $isLogEnabled=='0') return;
        }else{
            $secondaryChangeLogEnabled = ConfigSetting::where(['name' => 'SECONDARY_CHANGE_LOG', 'status' => 1])->value('value');
            if (empty($secondaryChangeLogEnabled) || $secondaryChangeLogEnabled=='0') return;
        }

        // Prepare payload
        $payload = match ($action) {
            'delete' => null,
            default  => self::sanitize($model),
        };
        
        // Insert change log
        ChangeLog::create([
            'server_id'  => config('sync.server_id'),
            'table_name' => $model->getTable(),
            'record_id'  => $model->getKey(),
            'action'     => $action,
            'payload'    => $payload ? json_encode($payload) : null,
        ]);

        \Log::info("Change log inserted: {$action} on {$model->getTable()} ID {$model->getKey()}");

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
                // 'created_at',
                // 'updated_at',
            ])
            ->toArray();
    }
}
