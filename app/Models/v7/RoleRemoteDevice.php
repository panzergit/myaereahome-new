<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class RoleRemoteDevice extends Model
{
    protected $fillable = [
      'role_id','account_id','device_id','device_svn','status'
    ]; 

   

}
