<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryUsageSync extends Model
{
    protected $table = 'inventory_usage_syncs';
    protected $fillable = [
        'external_id',
        'payload',
        'applied_at',
    ];
    protected $casts = [
        'payload' => 'array',
        'applied_at' => 'datetime',
    ];
}
