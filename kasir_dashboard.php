<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'kasir') {
    header("Location: index.php");
    exit();
}

$query_transaksi = mysqli_query($conn, "
    SELECT COUNT(*) AS total_transaksi 
    FROM transaksi 
    WHERE DATE(tanggal) = CURDATE()
");
$data_transaksi = mysqli_fetch_assoc($query_transaksi);

$query_pendapatan = mysqli_query($conn, "
    SELECT SUM(total_harga) AS total_pendapatan 
    FROM transaksi 
    WHERE DATE(tanggal) = CURDATE()
");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);

$query_barang_terlaris = mysqli_query($conn, "
    SELECT b.nama_barang, SUM(t.jumlah) AS total_terjual
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    WHERE DATE(t.tanggal) = CURDATE()
    GROUP BY b.nama_barang
    ORDER BY total_terjual DESC
    LIMIT 1
");
$data_barang_terlaris = mysqli_fetch_assoc($query_barang_terlaris);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Alat Tulis Ika - Kasir Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <!-- Header -->
    <header class="bg-white shadow-md fixed w-full top-0 z-40">
        <div class="flex justify-between items-center px-6 py-4">
            <h1 class="text-xl font-bold text-blue-700">📝 Toko Alat Tulis Ika</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-blue-800">Halo, <?= htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    🚪 Keluar
                </a>
            </div>
        </div>
    </header>

    <!-- Layout Utama -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="bg-blue-50 shadow-lg w-64 fixed top-16 bottom-0 left-0 z-30">
            <nav class="p-4">
                <div class="mb-6 text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-2xl text-white">💼</span>
                    </div>
                    <p class="text-sm font-semibold text-blue-800">KASIR</p>
                </div>
                <ul class="space-y-2">
                    <li><a href="kasir_dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-blue-600 bg-blue-100 rounded-lg"><span>🏠</span><span>Dashboard</span></a></li>
                    <li><a href="kasir_transaksi.php" class="flex items-center space-x-3 px-4 py-3 text-blue-800 hover:bg-blue-100 rounded-lg"><span>💸</span><span>Transaksi</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 mt-24 p-6">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-blue-800 mb-2">Selamat Bekerja, Kasir!</h2>
                <p class="text-gray-600">Kelola transaksi penjualan dengan mudah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-600 mb-1">Transaksi Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600"><?= $data_transaksi['total_transaksi'] ?? 0; ?></p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-600 mb-1">Pendapatan Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600">Rp <?= number_format($data_pendapatan['total_pendapatan'] ?? 0, 0, ',', '.'); ?></p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                    <p class="text-sm text-gray-600 mb-1">Barang Terlaris</p>
                    <p class="text-lg font-bold text-orange-600"><?= $data_barang_terlaris['nama_barang'] ?? '-'; ?></p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">⚡ Aksi Cepat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="kasir_transaksi.php" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center">
                        💸 Buat Transaksi Baru
                    </a>
                    <a href="kasir_stok.php" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center">
                        📦 Cek Stok Barang
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-600 fixed bottom-0 left-64 right-0">
    © 2025 – Aplikasi Penjualan Sederhana by Ika Desy Pramita Sari
    </footer>

</body>
</html>
