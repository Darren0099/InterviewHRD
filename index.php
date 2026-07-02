<?php
include 'koneksi.php';
$regionalAktif = $_GET['regional'] ?? 'SUMSEL';
$search = $_GET['search'] ?? '';

$divisiList = [
    'Graphic Design' => 4,
    'Content Creator' => 3,
    'Finance' => 4,
    'Project Management' => 7,
    'Human Resource' => 7,
    'Public Relation' => 7,
    'Secretary' => 5, 
    'Vice Leader' => 5,
    'Leader' => 5,
    'Social Media Management' => 1
];

// 1. Hitung Statistik Utama (Bagian 6.1)
$qTotal = mysqli_query($conn, "SELECT COUNT(*) total FROM penilaian WHERE regional='$regionalAktif'");
$totalPendaftar = mysqli_fetch_assoc($qTotal)['total'] ?? 0;

$qAvg = mysqli_query($conn, "SELECT AVG(total) rata FROM penilaian WHERE regional='$regionalAktif'");
$rataGlobal = round(mysqli_fetch_assoc($qAvg)['rata'] ?? 0);

$qMax = mysqli_query($conn, "SELECT MAX(total) nilai FROM penilaian WHERE regional='$regionalAktif'");
$nilaiMaxGlobal = mysqli_fetch_assoc($qMax)['nilai'] ?? 0;

// 2. Mengumpulkan Data Divisi & Menghitung Total TOP Akurat
$totalTop = 0;
$chartLabels = [];
$chartData = [];
$statsDivisi = [];

foreach ($divisiList as $div => $limit) {
    $qStats = mysqli_query($conn, "
        SELECT 
            COUNT(*) AS jml,
            MAX(total) AS max_nilai,
            AVG(total) AS avg_nilai
        FROM penilaian
        WHERE regional='$regionalAktif' AND divisi='$div'
    ");
    $dStats = mysqli_fetch_assoc($qStats);
    
    $jmlDivisi = $dStats['jml'] ?? 0;
    $topDivisi = min($jmlDivisi, $limit);
    $totalTop += $topDivisi; 

    $chartLabels[] = $div;
    $chartData[] = $jmlDivisi;
    
    $statsDivisi[$div] = [
        'jml' => $jmlDivisi,
        'top' => $topDivisi,
        'max' => $dStats['max_nilai'] ?? 0,
        'avg' => round($dStats['avg_nilai'] ?? 0)
    ];
}

// Menambahkan warna ke-10 untuk menyeimbangkan visual card divisi
$warna = [
    '#FFE7D1', '#E9E7FF', '#DDF6FF', 
    '#E3FCEF', '#FDE7F3', '#FFF6D9', 
    '#D6F5FF', '#E7F0FF', '#F0E7FF', '#E7FFF3'
];
$i = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Kandidat YRI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="assets/css/style.css">
<script>
const REGIONAL_AKTIF = "<?= $regionalAktif ?>";
</script>
<script src="assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
  .card-divisi-clickable {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .card-divisi-clickable:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
  }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">
  <span class="navbar-brand fw-bold">Kandidat Youth Ranger Indonesia</span>
  <form class="d-flex ms-3" method="get">
    <input type="hidden" name="regional" value="<?= $regionalAktif ?>">
    <input class="form-control" name="search" placeholder="Cari kandidat" value="<?= htmlspecialchars($search) ?>">
  </form>
  <div class="ms-auto d-flex gap-2">
    <button class="btn btn-danger" onclick="hapusRegional()">Hapus Data Regional</button>
    <select class="form-select" onchange="location='?regional='+this.value">
      <?php foreach(['SUMSEL','LAMPUNG','JAMBI','BENGKULU','BANGKA BELITUNG'] as $r): ?>
      <option <?= $r==$regionalAktif?'selected':'' ?>><?= $r ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-primary" onclick="tambahData()">+ Tambah Data</button>
    <div class="dropdown">
      <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">Export</button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="export_excel.php?regional=<?= $regionalAktif ?>">Excel</a></li>
        <li><a class="dropdown-item" href="export_pdf.php?regional=<?= $regionalAktif ?>">PDF</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">

  <div class="row mb-4 g-3">
    <div class="col-md-3">
      <div class="p-3 bg-white border rounded shadow-sm text-center">
        <h2 class="fw-bold text-primary"><?= $totalPendaftar ?></h2>
        <span class="text-muted text-uppercase small font-monospace">Total Pendaftar</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 bg-white border rounded shadow-sm text-center">
        <h2 class="fw-bold text-success"><?= $totalTop ?></h2>
        <span class="text-muted text-uppercase small font-monospace">Kandidat TOP</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 bg-white border rounded shadow-sm text-center">
        <h2 class="fw-bold text-warning"><?= $nilaiMaxGlobal ?></h2>
        <span class="text-muted text-uppercase small font-monospace">Nilai Tertinggi</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 bg-white border rounded shadow-sm text-center">
        <h2 class="fw-bold text-info"><?= $rataGlobal ?></h2>
        <span class="text-muted text-uppercase small font-monospace">Rata-rata Nilai</span>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <?php foreach($divisiList as $div => $limit): 
        $sd = $statsDivisi[$div];
        $persen = min(100, ($sd['jml'] / 100) * 100);
    ?>
    <div class="col-md-3">
      <div class="card-divisi card-divisi-clickable p-3 rounded h-100 position-relative d-flex flex-column justify-content-between" 
           style="background:<?= $warna[$i++ % count($warna)] ?>" 
           onclick="showTop('<?= $div ?>')">
           
        <div>
          <div class="d-flex justify-content-between align-items-start">
            <h5 class="fw-bold text-dark m-0 pe-2"><?= $div ?></h5>
            <span class="badge bg-dark">TOP <?= $sd['top'] >= $limit ? '✓' : '✕' ?></span>
          </div>
          
          <div class="mt-3">
            <small class="d-block text-secondary fw-semibold"><?= $sd['jml'] ?> Orang</small>
            <div class="progress my-1" style="height: 6px;"><div class="progress-bar bg-dark" style="width:<?= $persen ?>%"></div></div>
            <small class="text-dark">TOP: <b><?= $sd['top'] ?></b> / <?= $limit ?></small>
          </div>
        </div>

        <div class="mt-3 pt-2 border-top border-secondary-subtle d-flex justify-content-between text-center">
          <div>
            <div class="small text-muted" style="font-size: 0.75rem;">Rata-rata</div>
            <strong class="text-dark"><?= $sd['avg'] ?></strong>
          </div>
          <div>
            <div class="small text-muted" style="font-size: 0.75rem;">Nilai Max</div>
            <strong class="text-dark"><?= $sd['max'] ?></strong>
          </div>
        </div>

      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="bg-white p-4 border rounded shadow-sm mt-5">
    <h5 class="fw-bold mb-3">Jumlah Kandidat per Divisi</h5>
    <div style="position: relative; height:300px; width:100%">
      <canvas id="grafikDivisi"></canvas>
    </div>
  </div>

  <div class="bg-white p-4 border rounded shadow-sm mt-4 mb-5">
    <h5 class="fw-bold">Papan Nilai Kandidat (Global / Top 10)</h5>
    <table class="table table-hover align-middle mt-3">
      <thead class="table-light">
        <tr>
          <th style="width: 8%">Rank</th>
          <th>Nama</th>
          <th>Divisi</th>
          <th style="width: 12%">Nilai</th>
          <th style="width: 15%">Status Seleksi</th>
          <th style="width: 15%" class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
     <?php
      $qTable = mysqli_query($conn, "
          SELECT * FROM penilaian 
          WHERE regional='$regionalAktif' 
          AND nama_kandidat LIKE '%$search%' 
          ORDER BY total DESC 
          LIMIT 10
      ");

      $counterDivisi = [];
      $rankGlobal = 1;

      while($r = mysqli_fetch_assoc($qTable)):
          $div = $r['divisi'];
          
          if(!isset($counterDivisi[$div])) {
              $counterDivisi[$div] = 1;
          } else {
              $counterDivisi[$div]++;
          }

          $rankDivisiSaatIni = $counterDivisi[$div];
          $kuotaDivisi = $divisiList[$div] ?? 0;

          switch($rankGlobal) {
              case 1: $medal = "🥇"; break;
              case 2: $medal = "🥈"; break;
              case 3: $medal = "🥉"; break;
              default: $medal = $rankGlobal;
          }

          if ($rankDivisiSaatIni <= $kuotaDivisi) {
              $statusBadge = "<span class='badge bg-success shadow-sm'>Lolos Kuota</span>";
          } elseif ($rankDivisiSaatIni <= ($kuotaDivisi + 2)) { 
              $statusBadge = "<span class='badge bg-warning text-dark shadow-sm'>Cadangan</span>";
          } else {
              $statusBadge = "<span class='badge bg-secondary shadow-sm'>Tidak Lolos</span>";
          }
      ?>
      <tr>
        <td class="fw-bold text-center" style="font-size: 1.1rem;"><?= $medal ?></td>
        <td><?= htmlspecialchars($r['nama_kandidat']) ?></td>
        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($div) ?></span></td>
        <td>
          <?php
          if($r['total'] >= 85){
              echo "<span class='badge bg-success px-3 py-2'>".$r['total']."</span>";
          } elseif($r['total'] >= 70){
              echo "<span class='badge bg-warning text-dark px-3 py-2'>".$r['total']."</span>";
          } else {
              echo "<span class='badge bg-danger px-3 py-2'>".$r['total']."</span>";
          }
          ?>
        </td>
        <td><?= $statusBadge ?></td>
        <td class="text-center">
          <button class="btn btn-sm btn-outline-warning me-1" onclick="editData(<?= $r['id'] ?>)">Edit</button>
          <button class="btn btn-sm btn-outline-danger" onclick="hapusData(<?= $r['id'] ?>)">Hapus</button>
        </td>
      </tr>
      <?php 
          $rankGlobal++;
      endwhile; 
      ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    new Chart(
        document.getElementById("grafikDivisi"),
        {
            type: "bar",
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: "Jumlah Pelamar Aktif",
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: 'rgba(33, 37, 41, 0.75)',
                    borderColor: 'rgba(33, 37, 41, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        }
    );
});
</script>
<style>
  /* =======================
   FOOTER
======================= */

.footer{
    margin-top:60px;
    background:#ffffff;
    border-top:1px solid #e5e7eb;
    padding:25px 0;
    color:#6b7280;
    box-shadow:0 -3px 15px rgba(0,0,0,.03);
}

.footer h6{
    color:#111827;
    font-weight:700;
}

.footer p{
    margin-bottom:8px;
    font-size:14px;
}

.footer small{
    font-size:13px;
    line-height:22px;
}
</style>
<footer class="footer mt-5">
    <div class="container text-center">

        <h6 class="mb-2 fw-bold">
            Kandidat Youth Ranger Indonesia
        </h6>

        <p class="mb-1">
            Sistem Penilaian Interview Human Resource Development (HRD)
        </p>

        <small>
            © 2026 Youth Ranger Indonesia District Sumatera 2
            <br>
            Developed by Tim Human Resource District Sumatera 2
            <br>
            Under the supervision of <b>Youth Ranger Indonesia Pusat</b>
        </small>

    </div>
</footer>
</body>
</html>

```