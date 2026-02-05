<?php

namespace App\Traits;

use App\Services\ChangeLogService;

trait Syncable
{
    protected static function bootSyncable()
    {
        static::created(function ($model) {
            ChangeLogService::log('insert', $model);
        });

        static::updated(function ($model) {
            ChangeLogService::log('update', $model);
        });

        static::deleted(function ($model) {
            ChangeLogService::log('delete', $model);
        });
    }
}
