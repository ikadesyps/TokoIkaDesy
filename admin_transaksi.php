<?php 
include 'koneksi.php';

// Filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Pencarian nama pembeli
$cari_pembeli = isset($_GET['cari_pembeli']) ? $_GET['cari_pembeli'] : '';

// Query dasar
$query = "SELECT t.*, b.nama_barang, br.nama_pembeli 
          FROM transaksi t
          JOIN barang b ON t.id_barang=b.id_barang
          JOIN pembeli br ON t.id_pembeli=br.id_pembeli
          WHERE 1";

// Tambahkan filter tanggal
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $query .= " AND DATE(t.tanggal) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

// Tambahkan pencarian nama pembeli
if (!empty($cari_pembeli)) {
    $query .= " AND br.nama_pembeli LIKE '%$cari_pembeli%'";
}

$query .= " ORDER BY t.id_transaksi DESC";
$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Transaksi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<header class="bg-white shadow-md p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-blue-800">📊 Laporan Transaksi</h1>
    <div class="space-x-2">
        <link rel="stylesheet" href="style.css">
        <a href="admin_dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">🏠 Kembali</a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">🖨 Cetak</button>
    </div>
</header>

<div class="p-6">
    <form method="GET" class="mb-4 flex flex-wrap gap-3">
        <div>
            <label class="block text-sm">Dari Tanggal:</label>
            <input type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>" class="border px-2 py-1 rounded">
        </div>
        <div>
            <label class="block text-sm">Sampai Tanggal:</label>
            <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir ?>" class="border px-2 py-1 rounded">
        </div>
        <div>
            <label class="block text-sm">Cari Pembeli:</label>
            <input type="text" name="cari_pembeli" value="<?= $cari_pembeli ?>" placeholder="Nama pembeli" class="border px-2 py-1 rounded">
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">🔍 Filter</button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full border text-sm">
            <thead class="bg-blue-100">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Nama Pembeli</th>
                    <th class="p-2 border">Nama Barang</th>
                    <th class="p-2 border">Jumlah</th>
                    <th class="p-2 border">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_pendapatan = 0;
                $total_transaksi = 0;

                while ($row = mysqli_fetch_assoc($data)) {
                    $total_transaksi++;
                    $total_pendapatan += $row['total_harga'];

                    echo "
                    <tr class='border-b hover:bg-gray-50'>
                        <td class='p-2 border'>{$row['id_transaksi']}</td>
                        <td class='p-2 border'>{$row['tanggal']}</td>
                        <td class='p-2 border'>{$row['nama_pembeli']}</td>
                        <td class='p-2 border'>{$row['nama_barang']}</td>
                        <td class='p-2 border text-center'>{$row['jumlah']}</td>
                        <td class='p-2 border text-right font-semibold text-green-600'>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                    </tr>";
                }

                if ($total_transaksi == 0) {
                    echo "<tr><td colspan='6' class='text-center p-4 text-gray-500'>Tidak ada data transaksi</td></tr>";
                }
                ?>
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td colspan="5" class="text-right p-2 border">Total Pendapatan</td>
                    <td class="text-right p-2 border text-green-700">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
@media print {
    header, form, button, a {
        display: none !important;
    }
    table {
        font-size: 12px;
    }
}
</style>

</body>
</html>
