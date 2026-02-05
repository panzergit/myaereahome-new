<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Syncable;

class UserRemoteDevice extends Model
{
    use Syncable;

    protected $fillable = [
      'user_id','building_id','unit_no','account_id','device_id','device_svn','status'
    ];
}
