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
            <a href="" class="btn btn-sm btn-outline-primary"><i class="fas fa-sync"></i> Refresh</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase fw-bold mb-2">Total Aktivasi Lampu</div>
                    <h1 class="display-5 fw-bold text-dark">{{ $totalNyala }}</h1>
                    <span class="badge bg-light text-muted">Berdasarkan Database</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase fw-bold mb-2">Rata-rata Akurasi YOLO</div>
                    <h1 class="display-5 fw-bold text-primary">{{ number_format($avgConfidence * 100, 1) }}%</h1>
                    <span class="badge bg-light text-muted">Validasi Computer Vision</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 {{ $statusSekarang == 'LAMP_ON' ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                <div class="card-body text-center">
                    <div class="text-white-50 small text-uppercase fw-bold mb-2">Status Lampu Saat Ini</div>
                    <h1 class="display-5 fw-bold">
                        <i class="fas {{ $statusSekarang == 'LAMP_ON' ? 'fa-lightbulb' : 'fa-moon' }}"></i>
                        {{ $statusSekarang == 'LAMP_ON' ? 'NYALA' : 'MATI' }}
                    </h1>
                    <span class="text-white-50 small">Live dari Database</span>
                </div>
            </div>
        </div>
    </div>

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
                        <th>Akurasi YOLO</th>
                    </tr>
                </thead>
                <tbody>
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
</body>

<script>
    function refreshData() {
        fetch('/api/stats-dashboard') // Sesuaikan URL route kamu
            .then(response => response.json())
            .then(data => {
                // Update Angka Total
                document.getElementById('total-nyala').innerText = data.totalNyala;
                document.getElementById('avg-conf').innerText = data.avgConfidence;
                
                // Update Card Status
                const statusCard = document.getElementById('status-card');
                const statusText = document.getElementById('status-text');
                
                if(data.status === 'NYALA') {
                    statusCard.classList.remove('bg-secondary');
                    statusCard.classList.add('bg-success');
                    statusText.innerText = 'NYALA';
                } else {
                    statusCard.classList.remove('bg-success');
                    statusCard.classList.add('bg-secondary');
                    statusText.innerText = 'MATI';
                }

                // Update Tabel (Opsional: Rombak tabel lewat JS)
            });
    }

    // Jalankan tiap 2 detik
    setInterval(refreshData, 2000);
</script>
</html>