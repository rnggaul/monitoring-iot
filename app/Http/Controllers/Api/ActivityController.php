<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LampActivity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        \App\Models\LampActivity::create([
            'device_id'        => $request->device_id ?? 'CCTV-YOLOv8',
            'event_type'       => $request->event_type,
            'confidence_score' => $request->confidence, // Kirim dari Python
            'recorded_at'      => now(),
        ]);

        return response()->json(['message' => 'Data logged!'], 201);
    }
}
