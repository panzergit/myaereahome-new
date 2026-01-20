<?php

namespace App\Models\v7;

use Illuminate\Database\Eloquent\Model;

class RoleDevice extends Model
{
    protected $fillable = [
      'role_id','account_id','device_id','device_svn','status'
    ]; 

   

}
