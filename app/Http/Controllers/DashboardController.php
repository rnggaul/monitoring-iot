<?php

namespace App\Http\Controllers;

use App\Models\LampActivity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() 
    {
        // 1. MENGAMBIL DATA AKTIVITAS
        // Mengambil 50 data terbaru untuk ditampilkan di tabel history
        $activities = \App\Models\LampActivity::orderBy('created_at', 'desc')->get();

        // 2. MENGHITUNG TOTAL AKTIVASI
        // Menghitung berapa kali lampu dinyalakan (mendukung format 'LAMP_ON' atau 'ON')
        $totalNyala = \App\Models\LampActivity::whereIn('event_type', ['LAMP_ON', 'ON'])->count();

        // 3. MENGHITUNG RATA-RATA AKURASI YOLO
        // Mengambil nilai rata-rata dari confidence_score (YOLO) yang tidak null
        $avgConfidence = \App\Models\LampActivity::whereNotNull('confidence_score')
                            ->avg('confidence_score') ?? 0;

        // 4. MENENTUKAN STATUS LAMPU SAAT INI
        // Mencari data terakhir untuk mengetahui apakah saat ini lampu sedang NYALA atau MATI
        $lastStatus = \App\Models\LampActivity::whereIn('event_type', ['LAMP_ON', 'LAMP_OFF', 'ON', 'OFF'])
                            ->latest()
                            ->first();

        $currentEventType = optional($lastStatus)->event_type;
        $isLightOn = in_array($currentEventType, ['LAMP_ON', 'ON']);

        // 5. LOGIKA PERHITUNGAN DURASI & BIAYA (PERBAIKAN REAL-TIME)
        $allLogs = \App\Models\LampActivity::orderBy('created_at', 'asc')->get();
        $totalSeconds = 0;
        $tempOnTime = null;

        foreach ($allLogs as $log) {
            if (in_array($log->event_type, ['LAMP_ON', 'ON'])) {
                $tempOnTime = $log->created_at;
            } elseif (in_array($log->event_type, ['LAMP_OFF', 'OFF']) && $tempOnTime != null) {
                // Hitung selisih detik antara ON dan OFF
                $totalSeconds += $tempOnTime->diffInSeconds($log->created_at);
                $tempOnTime = null; // Reset temp untuk sesi berikutnya
            }
        }

        // === BARIS PENYELAMAT (KONDISI JIKA LAMPU MASIH NYALA) ===
        if ($tempOnTime != null) {
            // Hitung durasi dari sejak menyala sampai DETIK INI (now())
            $totalSeconds += $tempOnTime->diffInSeconds(now());
        }

        // Perhitungan Biaya Listrik
        $wattLampu = 10; // Contoh: Lampu 10 Watt
        $totalJam = $totalSeconds / 3600;
        $kWh = ($wattLampu * $totalJam) / 1000;
        $tarifPLN = 1444.70; // Tarif R-1 Tegangan Rendah
        $totalBiaya = $kWh * $tarifPLN;

        // 6. MENYIAPKAN DATA DIAGRAM GARIS (7 HARI TERAKHIR)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            // Format label tanggal (contoh: 15 Mei)
            $chartLabels[] = $date->translatedFormat('d M'); 
            
            // Menghitung frekuensi lampu nyala per tanggal
            $chartData[] = \App\Models\LampActivity::whereDate('created_at', $date->toDateString())
                                    ->whereIn('event_type', ['LAMP_ON', 'ON'])
                                    ->count();
        }

        // 7. MENGIRIM DATA KE VIEW
        return view('dashboard', [
            'activities'    => $activities->take(50),
            'totalNyala'    => $totalNyala,
            'avgConfidence' => $avgConfidence,
            'statusSekarang'=> $isLightOn ? 'LAMP_ON' : 'LAMP_OFF',
            'chartLabels'   => $chartLabels,
            'chartData'     => $chartData,
            'totalBiaya'    => $totalBiaya, // Dikirim ke dashboard
            'totalJam'      => $totalJam      // Bisa ditampilkan jika butuh durasi total
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
