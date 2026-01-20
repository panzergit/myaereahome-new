<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSalaryLogsModel extends Model
{
    protected $table = 'user_salary_logs';
    protected $fillable = [
        'user_id', 'current_salary', 'new_salary', 'increament_amount', 'reason', 'increament_date'
    ];
}
