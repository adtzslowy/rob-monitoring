<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BmkgReading extends Model
{
    protected $table = 'bmkg_readings';

    public $timestamps = false;

    protected $fillable = [
        'wilayah',
        'adm4',
        'local_datetime',
        'utc_datetime',
        'suhu',
        'kelembapan',
        'kecepatan_angin',
        'arah_angin_deg',
        'arah_angin',
        'curah_hujan',
        'cuaca',
        'cuaca_icon',
        'fetched_at',
    ];

    protected $casts = [
        'local_datetime' => 'datetime',
        'utc_datetime' => 'datetime',
        'fetched_at' => 'datetime',
        'suhu' => 'float',
        'kelembapan' => 'float',
        'kecepatan_angin' => 'float',
        'arah_angin_deg' => 'float',
        'curah_hujan' => 'float',
    ];
}
