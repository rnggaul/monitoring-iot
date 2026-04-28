<?php

namespace App\Http\Controllers;

use App\Models\LampActivity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $activities = \App\Models\LampActivity::orderBy('created_at', 'desc')->get();

        // Hitung total nyala (sesuaikan dengan semua kemungkinan string 'ON')
        $totalNyala = \App\Models\LampActivity::whereIn('event_type', ['LAMP_ON', 'ON'])->count();

        $avgConfidence = \App\Models\LampActivity::whereNotNull('confidence_score')
                            ->avg('confidence_score') ?? 0;

        // AMBIL STATUS TERAKHIR (Cek semua keyword yang menandakan status lampu)
        $lastStatus = \App\Models\LampActivity::whereIn('event_type', ['LAMP_ON', 'LAMP_OFF', 'ON', 'OFF'])
                            ->latest()
                            ->first();

        // Logika penentuan status untuk tampilan
        $currentEventType = optional($lastStatus)->event_type;
        $isLightOn = in_array($currentEventType, ['LAMP_ON', 'ON']);

        return view('dashboard', [
            'activities' => $activities->take(50),
            'totalNyala' => $totalNyala,
            'avgConfidence' => $avgConfidence,
            'statusSekarang' => $isLightOn ? 'LAMP_ON' : 'LAMP_OFF'
        ]);
    }

    public function getStats() {
        $totalNyala = \App\Models\LampActivity::whereIn('event_type', ['LAMP_ON', 'ON'])->count();
        $avgConfidence = \App\Models\LampActivity::whereNotNull('confidence_score')->avg('confidence_score') ?? 0;
        $lastStatus = \App\Models\LampActivity::whereIn('event_type', ['LAMP_ON', 'LAMP_OFF', 'ON', 'OFF'])->latest()->first();
        $activities = \App\Models\LampActivity::latest()->take(10)->get();

        return response()->json([
            'totalNyala' => $totalNyala,
            'avgConfidence' => number_format($avgConfidence * 100, 1) . '%',
            'status' => in_array(optional($lastStatus)->event_type, ['LAMP_ON', 'ON']) ? 'NYALA' : 'MATI',
            'activities' => $activities
        ]);
    }
}
