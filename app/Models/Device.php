<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['name', 'lokasi', 'latitude', 'longitude', 'status', 'last_seen'];

    public function readings()
    {
        return $this->hasMany(SensorReading::class);
    }

    public function latestReading()
    {
        return $this->hasOne(SensorReading::class)->latestOfMany('timestamp');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'device_user', 'device_id', 'user_id')
            ->withTimestamps();
    }
}
