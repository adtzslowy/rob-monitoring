<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $table = "sensor_readings";

    protected $fillable = [
        'project',
        'device_id',
        'timestamp',
        'suhu',
        'tekanan_udara',
        'kelembapan',
        'ketinggian_air',
        'arah_angin',
        'kecepatan_angin',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
