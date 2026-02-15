<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    use HasFactory;
    protected $fillable = ['server_id', 'table_name', 'record_id', 'action', 'payload', 'synced', 'synced_at', 'status'];
}
