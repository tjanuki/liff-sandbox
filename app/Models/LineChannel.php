<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint_uuid',
        'liff_id',
        'channel_id',
        'channel_secret',
        'channel_access_token',
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}