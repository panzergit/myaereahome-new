<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class SyncController extends Controller
{
    public function apply(Request $request)
    {
        // Token validation
        if ((!$request->header('X-SYNC-TOKEN')) || $request->header('X-SYNC-TOKEN') !== config('sync.api_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $changes = $request->input('changes', []);

        if (empty($changes)) {
            return response()->json(['message' => 'No data'], 200);
        }

        // Prevent infinite sync loop
        config(['sync.disable_logging' => true]);

        Model::withoutEvents(function () use ($changes) {
            foreach ($changes as $log) {

                if (!Schema::hasTable($log['table_name'])) {
                    continue;
                }
                
                // DECODE PAYLOAD HERE
                $payload = is_string($log['payload'])
                ? json_decode($log['payload'], true)
                : $log['payload'];
                

                match ($log['action']) {
                    'insert', 'update' =>
                        DB::table($log['table_name'])->updateOrInsert(
                            ['id' => $log['record_id']],
                            $payload ?? []
                        ),

                    'delete' =>
                        DB::table($log['table_name'])
                            ->where('id', $log['record_id'])
                            ->delete(),
                };
            }
        });

        return response()->json(['message' => 'Sync applied'], 200);
    }

    public function fetch(Request $request)
    {
        // Token validation
        if ((!$request->header('X-SYNC-TOKEN')) || $request->header('X-SYNC-TOKEN') !== config('sync.api_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // How many records to send
        $limit = $request->input('limit', config('sync.batch_size', 100));

        // Fetch unsynced changes
        $changes = DB::table('change_logs')
            ->where('synced', 0)->orderBy('id')
            ->limit($limit)->get()
            ->map(function ($log) {
                // Decode payload here
                $log->payload = $log->payload
                    ? json_decode($log->payload, true)
                    : null;

                return $log;
            });

        return response()->json([
            'changes' => $changes,
            'count'   => $changes->count(),
        ]);
    }

    public function markSynced(Request $request)
    {
        // Token validation
        if ((!$request->header('X-SYNC-TOKEN')) || $request->header('X-SYNC-TOKEN') !== config('sync.api_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['message' => 'No IDs'], 200);
        }

        DB::table('change_logs')
            ->whereIn('id', $ids)
            ->update([
                'synced' => 1,
                'synced_at' => now(),
            ]);

        return response()->json([
            'message' => 'Marked as synced',
            'count' => count($ids),
        ]);
    }

    public function updatePrimaryState(Request $request)
    {
        // Token validation
        if ((!$request->header('X-SYNC-TOKEN')) || $request->header('X-SYNC-TOKEN') !== config('sync.api_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        DB::table('system_state')->updateOrInsert(
            ['key_name' => 'primary_status'],
            ['value' => 'up', 'updated_at' => $request->input('up_time', now())]
        );
        
        return response()->json([
            'message' => 'Updated primary state',
        ]);
    }
}
