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
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link active fw-semibold text-primary" href="#"><i class="fas fa-columns me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item px-3">
                    <div class="vr d-none d-lg-block" style="height: 20px;"></div>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-muted small">
                        <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i> 
                        Server: <strong>192.168.1.xxx</strong>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row g-4 mb-4 text-center">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Durasi Hari Ini</h6>
                    <h3 class="fw-bold">02:45:10</h3>
                    <p class="text-success small mb-0"><i class="fas fa-bolt"></i> Jam:Menit:Detik</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Frekuensi Lampu Nyala</h6>
                    <h3 class="fw-bold">14</h3>
                    <p class="text-muted small mb-0">Kali Aktivasi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Confidence Score YOLO</h6>
                    <h3 class="fw-bold text-primary">88%</h3>
                    <p class="text-muted small mb-0">Rata-rata Akurasi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100 bg-gradient-primary">
                <div class="card-body">
                    <h6 class="text-uppercase small">Status Sistem</h6>
                    <h3 class="fw-bold"><i class="fas fa-check-circle"></i> Aktif</h3>
                    <p class="small mb-0 text-white-50">Menunggu Trigger PIR...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-3 fw-bold">Tren Konsumsi Energi (7 Hari Terakhir)</div>
                <div class="card-body">
                    <canvas id="energyChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-3 fw-bold">Live Status</div>
                <div class="card-body text-center">
                    <div class="p-4 rounded-circle bg-light d-inline-block mb-3 shadow-sm">
                        <i class="fas fa-lightbulb fa-4x text-warning"></i>
                    </div>
                    <h5>Lampu Ruangan: <strong>NYALA</strong></h5>
                    <hr>
                    <p class="text-muted small">Terakhir diperbarui: <br> 25 April 2026, 10:35 WIB</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-5" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-3 fw-bold">Log Aktivitas Real-time</div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Event</th>
                        <th>Sumber Data</th>
                        <th>Akurasi YOLO</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10:35:12</td>
                        <td><span class="badge bg-info text-dark">HUMAN_DETECTED</span></td>
                        <td>CachyOS Laptop</td>
                        <td>92.4%</td>
                        <td><span class="badge bg-success">Lampu ON</span></td>
                    </tr>
                    <tr>
                        <td>10:30:05</td>
                        <td><span class="badge bg-warning text-dark">PIR_TRIGGERED</span></td>
                        <td>Ubuntu Server</td>
                        <td>-</td>
                        <td>Sistem Terjaga</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('energyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Durasi Lampu Nyala (Menit)',
                data: [45, 120, 60, 200, 150, 30, 10],
                fill: true,
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderColor: 'rgba(78, 115, 223, 1)',
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
</body>
</html>