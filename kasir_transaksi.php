<?php 
session_start();
include 'koneksi.php';

// --- Proteksi Akses ---
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'kasir' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit;
}

// === SIMPAN TRANSAKSI ===
if (isset($_POST['simpan'])) {
    $id_pembeli = mysqli_real_escape_string($conn, $_POST['id_pembeli']);
    $id_barang  = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $jumlah     = (int) $_POST['jumlah'];


    // --- Ambil data barang ---
    $q = mysqli_query($conn, "SELECT harga, stok FROM barang WHERE id_barang='$id_barang'");
    $barang = mysqli_fetch_assoc($q);

    if (!$barang) {
        echo "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Barang tidak ditemukan!</div>";
    } elseif ($jumlah > $barang['stok']) {
        echo "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Jumlah melebihi stok tersedia!</div>";
    } else {
        $total = $barang['harga'] * $jumlah;

        // --- AUTO GENERATE ID TRANSAKSI ---
        $cek = mysqli_query($conn, "SELECT id_transaksi FROM transaksi ORDER BY id_transaksi DESC LIMIT 1");
        $data = mysqli_fetch_assoc($cek);

        if ($data && preg_match('/TRX-(\d+)/', $data['id_transaksi'], $m)) {
            $lastID = (int) $m[1];
            $newID = 'TRX-' . str_pad($lastID + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newID = 'TRX-001';
        }

        // --- Simpan transaksi ---
        $insert_query = "
            INSERT INTO transaksi (id_transaksi, id_pembeli, id_barang, jumlah, total_harga, tanggal)
            VALUES ('$newID', '$id_pembeli', '$id_barang', '$jumlah', '$total', NOW())
        ";

        if (mysqli_query($conn, $insert_query)) {
            // Kurangi stok barang
            mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang='$id_barang'");
            header("Location: kasir_transaksi.php"); // hindari meta refresh
            exit;
        } else {
            echo "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>
                Gagal menyimpan transaksi: " . mysqli_error($conn) . "
            </div>";
        }
    }
}

// === HAPUS TRANSAKSI ===
if (isset($_GET['hapus'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Ambil id_barang dan jumlah untuk mengembalikan stok
    $q = mysqli_query($conn, "SELECT id_barang, jumlah FROM transaksi WHERE id_transaksi='$id_transaksi'");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $id_barang = $row['id_barang'];
        $jumlah = (int) $row['jumlah'];

        // Kembalikan stok barang
        mysqli_query($conn, "UPDATE barang SET stok = stok + $jumlah WHERE id_barang='$id_barang'");

        // Hapus transaksi
        mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi='$id_transaksi'");

        header("Location: kasir_transaksi.php"); // redirect agar halaman bersih
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Alat Tulis Ika - Transaksi Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<!-- HEADER -->
<header class="bg-white shadow-lg fixed w-full top-0 z-50">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center space-x-2 text-xl font-bold text-blue-800">
            <span role="img" aria-label="Alat Tulis" class="text-2xl">📝</span>
            <h1>Toko Alat Tulis Ika</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="kasir_dashboard.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">🏠 Kembali</a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">🚪 Keluar</a>
        </div>
    </div>
</header>

<!-- MAIN -->
<div class="flex pt-24">
    <main class="flex-1 p-6">
        <h2 class="text-2xl font-bold text-blue-800 mb-4">💸 Transaksi Kasir</h2>

        <!-- FORM TRANSAKSI -->
        <form method="POST" class="bg-white p-6 rounded-xl shadow-md mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Nama Pembeli</label>
                    <select name="id_pembeli" required class="w-full border px-3 py-2 rounded">
                        <option value="">-- Pilih Pembeli --</option>
                        <?php
                        $pembeli = mysqli_query($conn, "SELECT * FROM pembeli ORDER BY nama_pembeli ASC");
                        while ($row = mysqli_fetch_assoc($pembeli)) {
                            echo "<option value='{$row['id_pembeli']}'>{$row['nama_pembeli']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-1">Nama Barang</label>
                    <select name="id_barang" required class="w-full border px-3 py-2 rounded">
                        <option value="">-- Pilih Barang --</option>
                        <?php
                        $barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
                        while ($row = mysqli_fetch_assoc($barang)) {
                            echo "<option value='{$row['id_barang']}'>{$row['nama_barang']} (Stok: {$row['stok']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-1">Jumlah</label>
                    <input type="number" name="jumlah" required min="1" class="w-full border px-3 py-2 rounded">
                </div>
            </div>
            <button type="submit" name="simpan" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg mt-4">💾 Simpan</button>
        </form>

        <!-- TABEL TRANSAKSI -->
        <div class="bg-white p-6 rounded-xl shadow-md overflow-x-auto">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">📋 Transaksi Hari Ini</h3>
            <table class="w-full border text-sm">
                <thead class="bg-blue-100">
                    <tr>
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Pembeli</th>
                        <th class="p-2 border">Barang</th>
                        <th class="p-2 border">Harga</th>
                        <th class="p-2 border">Jumlah</th>
                        <th class="p-2 border">Total</th>
                        <th class="p-2 border">Tanggal</th>
                        <th class="p-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "
                        SELECT t.*, b.nama_barang, b.harga, p.nama_pembeli 
                        FROM transaksi t
                        JOIN barang b ON t.id_barang = b.id_barang
                        JOIN pembeli p ON t.id_pembeli = p.id_pembeli
                        ORDER BY CAST(SUBSTRING(t.id_transaksi, 5) AS UNSIGNED) DESC
                    ");

                    if (mysqli_num_rows($data) == 0) {
                        echo "<tr><td colspan='8' class='text-center p-4 text-gray-500'>Belum ada transaksi.</td></tr>";
                    } else {
                        while ($row = mysqli_fetch_assoc($data)) {
                            echo "
                            <tr class='border-b hover:bg-gray-50'>
                                <td class='p-2 border'>{$row['id_transaksi']}</td>
                                <td class='p-2 border'>{$row['nama_pembeli']}</td>
                                <td class='p-2 border'>{$row['nama_barang']}</td>
                                <td class='p-2 border'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                                <td class='p-2 border'>{$row['jumlah']}</td>
                                <td class='p-2 border font-semibold text-green-600'>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                                <td class='p-2 border'>" . date('d/m H:i', strtotime($row['tanggal'])) . "</td>
                                <td class='p-2 border text-center'>
                                    <a href='cetak_struk.php?id={$row['id_transaksi']}' target='_blank' class='bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs'>🖨 Cetak</a>
                                    <a href='?hapus={$row['id_transaksi']}' onclick=\"return confirm('Hapus transaksi #{$row['id_transaksi']}? Stok akan dikembalikan.');\" class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs'>🗑 Hapus</a>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
