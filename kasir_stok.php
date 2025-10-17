<?php
session_start();
include 'koneksi.php'; // sesuaikan dengan letak file koneksi.php kamu

// Pastikan hanya kasir yang bisa akses
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'kasir') {
    header("Location: ../index.php");
    exit;
}

// Ambil data barang dari database
$query = "SELECT * FROM barang ORDER BY nama_barang ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Stok Barang - Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto mt-10 bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">📦 Cek Stok Barang</h1>
            <link rel="stylesheet" href="style.css">
            <a href="kasir_dashboard.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                🏠 Dashboard
            </a>
        </div>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-blue-100">
                    <th class="border border-gray-300 px-3 py-2 text-left">No</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Nama Barang</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">Stok</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Harga (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "
                        <tr class='hover:bg-gray-50'>
                            <td class='border px-3 py-2'>{$no}</td>
                            <td class='border px-3 py-2'>".htmlspecialchars($row['nama_barang'])."</td>
                            <td class='border px-3 py-2 text-center'>{$row['stok']}</td>
                            <td class='border px-3 py-2 text-right'>Rp ".number_format($row['harga'],0,',','.')."</td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center p-4 text-gray-500'>Belum ada data barang</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
