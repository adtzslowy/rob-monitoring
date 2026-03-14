<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'telegram_chat_id',
        'notifikasi_aktif',
        'notifikasi_waspada',
        'notifikasi_siaga',
        'notifikasi_bahaya'
    ]; 

    protected $casts = [
        'notifikasi_aktif'=> 'boolean',
        'notifikasi_waspada'  => 'boolean',
        'notifikasi_siaga'    => 'boolean',
        'notifikasi_bahaya'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
