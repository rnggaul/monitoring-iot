<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LampActivity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data yang masuk dari script Python
        $validated = $request->validate([
            'event_type' => 'required|string',
            'confidence_score' => 'nullable|numeric',
        ]);

        // Simpan ke database di Ubuntu Server
        $log = LampActivity::create([
            'event_type' => $validated['event_type'],
            'confidence_score' => $validated['confidence_score'] ?? null,
            'recorded_at' => now(),
        ]);

        return response()->json(['message' => 'Data berhasil dicatat', 'data' => $log], 201);
    }
}
