<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Monitoring - Skripsi Raul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); color: white; }
        .table-responsive { max-height: 400px; overflow-y: auto; }
        canvas { width: 100% !important; height: 300px !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#" style="color: #4e73df;">
            <div class="bg-primary text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <span class="d-block lh-1">RAUL SMART-HOME</span>
                <small class="text-muted fw-normal" style="font-size: 0.7rem;">IoT Energy Monitoring System</small>
            </div>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-muted small me-3">
                <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i> 
                Server: <strong>{{ request()->ip() }}</strong>
            </span>
            <button onclick="location.reload()" class="btn btn-sm btn-outline-primary"><i class="fas fa-sync"></i> Refresh</button>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Row Statistik Utama -->
    <<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Total Aktivasi Lampu</div>
                <h1 class="display-5 fw-bold text-dark">{{ $totalNyala }}</h1>
                <span class="badge bg-light text-muted">Berdasarkan Database</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center">
                <div class="text-muted small text-uppercase fw-bold mb-2">Rata-rata Akurasi YOLO</div>
                <h1 class="display-5 fw-bold text-primary">{{ number_format($avgConfidence * 100, 1) }}%</h1>
                <span class="badge bg-light text-muted">Validasi Computer Vision</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div id="status-card" class="card border-0 shadow-sm rounded-4 h-100 {{ $statusSekarang == 'LAMP_ON' ? 'bg-success text-white' : 'bg-secondary text-white' }}">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="text-white-50 small text-uppercase fw-bold mb-2">Status Lampu Saat Ini</div>
                <h1 class="display-5 fw-bold">
                    <i id="status-icon" class="fas {{ $statusSekarang == 'LAMP_ON' ? 'fa-lightbulb' : 'fa-moon' }}"></i>
                    <span id="status-text">{{ $statusSekarang == 'LAMP_ON' ? 'NYALA' : 'MATI' }}</span>
                </h1>
                <span class="text-white-50 small">Live Monitoring</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body d-flex align-items-center justify-content-between px-4">
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1">Total Durasi Penggunaan</div>
                    <h1 class="fw-bold text-dark mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>
                        {{ floor($totalJam) }} <span class="fs-4 fw-normal text-muted">Jam</span> 
                        {{ round(($totalJam - floor($totalJam)) * 60) }} <span class="fs-4 fw-normal text-muted">Menit</span>
                    </h1>
                </div>
                <div class="text-end border-start ps-4">
                    <span class="d-block small text-muted text-uppercase fw-bold">Estimasi Energi</span>
                    <h4 class="mb-0 text-info fw-bold">{{ number_format(($totalJam * 10) / 1000, 4) }} <small>kWh</small></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="text-white-50 small text-uppercase fw-bold mb-2">Estimasi Biaya (PLN)</div>
                <h1 class="display-6 fw-bold mb-0">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h1>
                <span class="text-white-50 small" style="font-size: 0.7rem;">Tarif: Rp 1.444/kWh</span>
            </div>
        </div>
    </div>
</div>

    <!-- Row Diagram Garis (Penambahan Baru) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Aktivasi 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="activationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row History Log -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-3">
            <h5 class="fw-bold"><i class="fas fa-history me-2"></i>History Log Terkini</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu (DB)</th>
                        <th>Perangkat</th>
                        <th>Kejadian</th>
                        <th class="text-center">Akurasi YOLO</th>
                    </tr>
                </thead>
                <tbody id="log-table-body">
                    @forelse($activities as $log)
                    <tr>
                        <td class="small">{{ $log->created_at->format('H:i:s d-m-Y') }}</td>
                        <td><code class="text-primary">{{ $log->device_id }}</code></td>
                        <td>
                            <span class="badge {{ str_contains($log->event_type, 'ON') ? 'bg-success' : 'bg-danger' }}">
                                {{ $log->event_type }}
                            </span>
                        </td>
                        <td class="fw-bold text-center">
                            {{ $log->confidence_score ? number_format($log->confidence_score * 100, 1).'%' : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada data di database server.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Ambil data dari Laravel (Blade)
    // Jika data kosong, default ke 7 hari terakhir secara manual (client-side)
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartData = {!! json_encode($chartData) !!};

    const ctx = document.getElementById('activationChart').getContext('2d');
    
    // Inisialisasi Chart
    const activationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels, // Isinya: ["09 Mei", "10 Mei", ..., "15 Mei"]
            datasets: [{
                label: 'Frekuensi Lampu Menyala',
                data: chartData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3, // Membuat garis sedikit smooth
                pointRadius: 6,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 5, // Agar grafik tidak terlalu 'gepeng' jika data kecil
                    ticks: { 
                        stepSize: 1,
                        font: { size: 11 }
                    },
                    grid: { color: '#ebedef' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#4e73df',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    displayColors: false,
                    padding: 10
                }
            }
        }
    });

    // Fungsi Refresh Data Otomatis
    function refreshData() {
        fetch('/api/stats-dashboard') 
            .then(response => response.json())
            .then(data => {
                // 1. Update Statistik Angka
                document.getElementById('total-nyala').innerText = data.totalNyala;
                document.getElementById('avg-conf').innerText = data.avgConfidence + '%';
                
                // 2. Update Status Lampu Visual
                const statusCard = document.getElementById('status-card');
                const statusText = document.getElementById('status-text');
                const statusIcon = document.getElementById('status-icon');
                
                if(data.status === 'LAMP_ON' || data.status === 'NYALA') {
                    statusCard.className = 'card border-0 shadow-sm rounded-4 bg-success text-white';
                    statusText.innerText = 'NYALA';
                    statusIcon.className = 'fas fa-lightbulb';
                } else {
                    statusCard.className = 'card border-0 shadow-sm rounded-4 bg-secondary text-white';
                    statusText.innerText = 'MATI';
                    statusIcon.className = 'fas fa-moon';
                }

                // 3. Update Chart secara Real-time (Opsional)
                // Jika API kamu juga mengirim data chart terbaru:
                if(data.latestChartData) {
                    activationChart.data.datasets[0].data = data.latestChartData;
                    activationChart.update();
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Jalankan refresh setiap 2 detik
    setInterval(refreshData, 2000);
</script>
</body>
</html>