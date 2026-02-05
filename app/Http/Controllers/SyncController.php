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
}
