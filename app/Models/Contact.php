<?php

namespace App\Models;

use App\ContactStatus;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => ContactStatus::class,
    ];
}
