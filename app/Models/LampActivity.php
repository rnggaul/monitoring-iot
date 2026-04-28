<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LampActivity extends Model
{
    protected $fillable = [
        'device_id', 
        'event_type', 
        'confidence_score', 
        'recorded_at'
    ];
}
