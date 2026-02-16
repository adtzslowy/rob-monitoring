<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardLog extends Model
{
    protected $table = "dashboard_log";

    protected $fillable = [
        "project",
        "timestamp",
        "suhu",
        "arah_angin",
        "kecepatan_angin",
        "ketinggian_air",
        "kelembapan",
        "tekanan_udara"
    ];
}
