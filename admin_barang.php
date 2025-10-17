<?php
session_start();
include 'koneksi.php';

// Cek login admin (Perbaikan: Hapus ../ di index.php)
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); 
    exit;
}

// Ambil data barang
$data_barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY id_barang ASC");

// Jika pilih edit
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$id_edit'"));
}

// Tambah barang
if (isset($_POST['tambah'])) {
    $id_barang = mysqli_real_escape_string($conn, strtoupper($_POST['id_barang']));
    $nama_barang = mysqli_real_escape_string($conn, strtoupper($_POST['nama_barang']));
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);

    if ($stok < 0 || $harga < 0) {
        $error = "Harga dan stok tidak boleh negatif!";
    } else {
        // Query disederhanakan dan menggunakan variabel aman
        $insert_query = "INSERT INTO barang (id_barang, nama_barang, harga, stok) VALUES ('$id_barang','$nama_barang','$harga','$stok')";
        mysqli_query($conn, $insert_query);
        $success = "Barang berhasil ditambahkan!";
        header("Refresh:1; url=admin_barang.php");
        exit;
    }
}

// Update barang
if (isset($_POST['update'])) {
    $id_barang = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $nama_barang = mysqli_real_escape_string($conn, strtoupper($_POST['nama_barang']));
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);

    if ($stok < 0 || $harga < 0) {
        $error = "Harga dan stok tidak boleh negatif!";
    } else {
        $update_query = "UPDATE barang SET nama_barang='$nama_barang', harga='$harga', stok='$stok' WHERE id_barang='$id_barang'";
        mysqli_query($conn, $update_query);
        $success = "Barang berhasil diupdate!";
        header("Refresh:1; url=admin_barang.php");
        exit;
    }
}

// Hapus barang
if (isset($_GET['hapus'])) {
    $id_barang = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id_barang'");
    $success = "Barang berhasil dihapus!";
    header("Refresh:1; url=admin_barang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Barang - Toko Alat Tulis Ika</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-50">

<header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-blue-800">Admin Barang</h1>
    <div class="flex gap-2">
        <a href="admin_dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">🏠 Kembali</a>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Keluar</a>
    </div>
</header>

<main class="p-6">

    <?php 
    if(isset($error)) echo "<p class='text-red-600 mb-4'>$error</p>"; 
    if(isset($success)) echo "<p class='text-green-600 mb-4'>$success</p>";
    ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow-md mb-6 grid grid-cols-2 gap-4">
        <div>
            <label>ID Barang</label>
            <input type="text" name="id_barang" required class="w-full border px-3 py-2 rounded"
                value="<?= isset($edit_barang['id_barang']) ? $edit_barang['id_barang'] : '' ?>"
                <?= isset($edit_barang['id_barang']) ? 'readonly' : '' ?>>
        </div>
        <div>
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" required class="w-full border px-3 py-2 rounded"
                value="<?= isset($edit_barang['nama_barang']) ? $edit_barang['nama_barang'] : '' ?>">
        </div>
        <div>
            <label>Harga</label>
            <input type="number" name="harga" required class="w-full border px-3 py-2 rounded"
                value="<?= isset($edit_barang['harga']) ? $edit_barang['harga'] : '' ?>">
        </div>
        <div>
            <label>Stok</label>
            <input type="number" name="stok" required class="w-full border px-3 py-2 rounded"
                value="<?= isset($edit_barang['stok']) ? $edit_barang['stok'] : '' ?>">
        </div>
        <div class="col-span-2 flex gap-2 mt-4">
            <?php if(isset($edit_barang)): ?>
                <button type="submit" name="update" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">Update</button>
                <a href="admin_barang.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Batal</a>
            <?php else: ?>
                <button type="submit" name="tambah" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Tambah</button>
            <?php endif; ?>
        </div>
    </form>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Daftar Barang</h2>
        <table class="w-full border text-sm">
            <thead class="bg-blue-100">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Nama Barang</th>
                    <th class="p-2 border">Harga</th>
                    <th class="p-2 border">Stok</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($data_barang)): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2 border"><?= $row['id_barang'] ?></td>
                    <td class="p-2 border"><?= $row['nama_barang'] ?></td>
                    <td class="p-2 border">Rp <?= number_format($row['harga'],0,',','.') ?></td>
                    <td class="p-2 border"><?= $row['stok'] ?></td>
                    <td class="p-2 border text-center">
                        <a href="?edit=<?= $row['id_barang'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">Edit</a>
                        <a href="?hapus=<?= $row['id_barang'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs"
                            onclick="return confirm('Yakin ingin hapus barang ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>
</body>
</html>