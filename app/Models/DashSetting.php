<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashSetting extends Model
{
    protected $table = "dashboard_setting";

    protected $fillable = [
        "user_id",
        "theme",
        "selected_device_id",
        "selected_sensor",
        "selected_time_range",
        "visible_sensors",
    ];

    protected $casts = [
        "visible_sensors" => "array",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class, "selected_device_id");
    }
}
