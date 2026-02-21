<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashSetting extends Model
{
    protected $table = "dashboard_setting";
    protected $fillable = [
         "user_id","theme","selected_device_id","selected_sensor","select_time_range","visible_sensors",
    ];
}
