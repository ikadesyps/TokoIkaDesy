<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
include 'koneksi.php';

function getCount($conn, $query) {
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'] ?? 0;
    }
    return 0;
}

$total_barang = getCount($conn, "SELECT COUNT(*) AS total FROM barang");
$total_pembeli = getCount($conn, "SELECT COUNT(*) AS total FROM pembeli");
$total_transaksi = getCount($conn, "SELECT COUNT(*) AS total FROM transaksi");

$result = $conn->query("SELECT SUM(total_harga) AS total FROM transaksi");
$total_pendapatan = 0;
if ($result && $row = $result->fetch_assoc()) {
    $total_pendapatan = $row['total'] ? number_format($row['total'], 0, ',', '.') : '0';
}

$query_barang_terlaris = "
    SELECT b.nama_barang, SUM(t.jumlah) AS total_terjual
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    GROUP BY t.id_barang
    ORDER BY total_terjual DESC
    LIMIT 5
";
$barang_terlaris = $conn->query($query_barang_terlaris);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Toko Alat Tulis Ika - Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; }
.btn-keluar-custom {
  background-color: #EF4444;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
.btn-keluar-custom:hover { background-color: #DC2626; }
</style>
</head>

<body class="bg-gray-50 font-[Poppins]">

<!-- HEADER -->
<header class="bg-white shadow-md fixed w-full top-0 z-50">
  <div class="flex justify-between items-center px-6 py-3">
    <div class="flex items-center space-x-2">
      <span class="text-blue-600 text-xl">✏️</span>
      <h1 class="text-lg font-bold text-blue-800">Toko Alat Tulis Ika</h1>
    </div>
    <div class="flex items-center gap-3">
      <p class="text-gray-700 text-sm">Selamat Datang <span class="font-semibold text-blue-700"><?= htmlspecialchars($username) ?></span></p>
      <a href="logout.php" class="btn-keluar-custom text-white px-3 py-1.5 rounded-lg text-sm flex items-center space-x-1">
        <span>🚪</span><span>Keluar</span>
      </a>
    </div>
  </div>
</header>

<!-- LAYOUT UTAMA -->
<div class="flex">
  <!-- SIDEBAR -->
  <aside class="bg-blue-50 shadow-md w-56 fixed top-16 bottom-0 left-0 z-40">
    <nav class="p-4">
      <div class="text-center mb-4">
        <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-1">
          <span class="text-xl text-white">👑</span>
        </div>
        <p class="text-xs font-semibold text-blue-800 tracking-wide">ADMIN</p>
      </div>
      <ul class="space-y-1">
        <li><a href="admin_dashboard.php" class="block px-4 py-2 bg-blue-100 text-blue-700 rounded-md text-sm font-medium">🏠 Dashboard</a></li>
        <li><a href="admin_barang.php" class="block px-4 py-2 hover:bg-blue-100 rounded-md text-sm font-medium">📦 Barang</a></li>
        <li><a href="admin_pembeli.php" class="block px-4 py-2 hover:bg-blue-100 rounded-md text-sm font-medium">👥 Pembeli</a></li>
        <li><a href="admin_transaksi.php" class="block px-4 py-2 hover:bg-blue-100 rounded-md text-sm font-medium">💸 Transaksi</a></li>
      </ul>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="flex-1 ml-56 mt-24 p-6">
    <h2 class="text-2xl font-bold text-blue-800 mb-1">Selamat Datang, Admin Ika!</h2>
    <p class="text-gray-600 mb-6 text-sm">Kelola toko alat tulis dengan mudah dan efisien.</p>

    <!-- STATISTIK -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
        <p class="text-xs text-gray-500">Total Barang</p>
        <p class="text-2xl font-bold text-blue-600"><?= number_format($total_barang) ?></p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
        <p class="text-xs text-gray-500">Total Pembeli</p>
        <p class="text-2xl font-bold text-green-600"><?= number_format($total_pembeli) ?></p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
        <p class="text-xs text-gray-500">Total Transaksi</p>
        <p class="text-2xl font-bold text-purple-600"><?= number_format($total_transaksi) ?></p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
        <p class="text-xs text-gray-500">Total Pendapatan</p>
        <p class="text-2xl font-bold text-orange-600">Rp <?= $total_pendapatan ?></p>
      </div>
    </div>

    <!-- BARANG TERLARIS -->
    <div class="bg-white rounded-lg shadow p-5 mb-24">
      <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
        <span class="mr-2">🏆</span> Barang Terlaris
      </h3>

      <?php if ($barang_terlaris && $barang_terlaris->num_rows > 0): ?>
        <div class="space-y-2">
          <?php $no = 1; while ($row = $barang_terlaris->fetch_assoc()): ?>
            <div class="flex items-center justify-between p-3 <?= $no === 1 ? 'bg-gradient-to-r from-blue-50 to-blue-100' : 'bg-gray-50' ?> rounded-md">
              <div class="flex items-center space-x-2">
                <div class="w-6 h-6 <?= $no === 1 ? 'bg-blue-500' : 'bg-gray-400' ?> rounded-full flex items-center justify-center text-white font-bold text-xs"><?= $no ?></div>
                <span class="text-sm font-medium text-gray-800"><?= htmlspecialchars($row['nama_barang']) ?></span>
              </div>
              <span class="text-blue-600 text-sm font-semibold"><?= $row['total_terjual'] ?> terjual</span>
            </div>
          <?php $no++; endwhile; ?>
        </div>
      <?php else: ?>
        <p class="text-gray-500 text-xs">Belum ada data penjualan.</p>
      <?php endif; ?>
    </div>
  </main>
</div>

<!-- FOOTER -->
<footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-600 fixed bottom-0 left-56 right-0">
  © 2025 – Aplikasi Penjualan Sederhana by Ika Desy Pramita Sari
</footer>
</body>
</html>
