<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Threshold extends Model
{
    protected $fillable = [
        'device_id',
        'suhu_min',
        'suhu_max',
        'kelembapan_min',
        'kelembapan_max',
        'tekanan_udara_min',
        'tekanan_udara_max',
        'kecepatan_angin_min',
        'kecepatan_angin_max',
        'arah_angin_min',
        'arah_angin_max',
        'ketinggian_air_min',
        'ketinggian_air_max',
    ];

    protected $casts = [
        'suhu_min'            => 'float',
        'suhu_max'            => 'float',
        'kelembapan_min'      => 'float',
        'kelembapan_max'      => 'float',
        'tekanan_udara_min'   => 'float',
        'tekanan_udara_max'   => 'float',
        'kecepatan_angin_min' => 'float',
        'kecepatan_angin_max' => 'float',
        'arah_angin_min'      => 'float',
        'arah_angin_max'      => 'float',
        'ketinggian_air_min'  => 'float',
        'ketinggian_air_max'  => 'float',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}