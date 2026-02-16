<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetaMonitoring extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude'
    ];
}
