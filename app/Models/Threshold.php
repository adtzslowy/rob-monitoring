<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Threshold extends Model
{
    protected $fillable = [
        'suhu_aman',
        'suhu_waspada',
        'suhu_siaga',
        'suhu_bahaya',
        'kelembapan_aman',
        'kelembapan_waspada',
        'kelembapan_siaga',
        'kelembapan_bahaya',
        'tekanan_udara_aman',
        'tekanan_udara_waspada',
        'tekanan_udara_siaga',
        'tekanan_udara_bahaya',
        'kecepatan_angin_aman',
        'kecepatan_angin_waspada',
        'kecepatan_angin_siaga',
        'kecepatan_angin_bahaya',
        'arah_angin_aman',
        'arah_angin_waspada',
        'arah_angin_siaga',
        'arah_angin_bahaya',
        'ketinggian_air_aman',
        'ketinggian_air_waspada',
        'ketinggian_air_siaga',
        'ketinggian_air_bahaya',
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
