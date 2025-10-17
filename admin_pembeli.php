<?php
session_start();
include 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data pembeli
$data_pembeli = mysqli_query($conn, "SELECT * FROM pembeli ORDER BY id_pembeli ASC");

// Jika pilih edit
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_pembeli = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pembeli WHERE id_pembeli='$id_edit'"));
}

// Tambah pembeli
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat_pembeli']);

    mysqli_query($conn, "INSERT INTO pembeli (nama_pembeli, alamat) VALUES ('$nama','$alamat')");
    $success = "Pembeli berhasil ditambahkan!";
    header("Refresh:1; url=admin_pembeli.php");
}

// Update pembeli
if (isset($_POST['update'])) {
    $id = $_POST['id_pembeli'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat_pembeli']);

    mysqli_query($conn, "UPDATE pembeli SET nama_pembeli='$nama', alamat='$alamat' WHERE id_pembeli='$id'");
    $success = "Data pembeli berhasil diupdate!";
    header("Refresh:1; url=admin_pembeli.php");
}

// Hapus pembeli
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pembeli WHERE id_pembeli='$id'");
    $success = "Data pembeli berhasil dihapus!";
    header("Refresh:1; url=admin_pembeli.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Pembeli - Toko Alat Tulis Ika</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-50">

<header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-blue-800">Admin Pembeli</h1>
    <div class="flex gap-2">
        <a href="admin_dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">🏠 Kembali</a>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Keluar</a>
    </div>
</header>

<main class="p-6">

    <?php 
    if(isset($success)) echo "<p class='text-green-600 mb-4'>$success</p>";
    ?>

    <!-- Form Tambah / Update Pembeli -->
    <form method="POST" class="bg-white p-6 rounded-xl shadow-md mb-6 grid grid-cols-2 gap-4">
        <div>
            <label>Nama Pembeli</label>
            <input type="text" name="nama_pembeli" required class="w-full border px-3 py-2 rounded"
                value="<?= isset($edit_pembeli['nama_pembeli']) ? $edit_pembeli['nama_pembeli'] : '' ?>">
        </div>
        <div>
            <label>Alamat</label>
            <input type="text" name="alamat_pembeli" required class="w-full border px-3 py-2 rounded"
                value="<?= isset($edit_pembeli['alamat']) ? $edit_pembeli['alamat'] : '' ?>">
        </div>
        <div class="col-span-2 flex gap-2 mt-4">
            <?php if(isset($edit_pembeli)): ?>
                <input type="hidden" name="id_pembeli" value="<?= $edit_pembeli['id_pembeli'] ?>">
                <button type="submit" name="update" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">Update</button>
                <a href="admin_pembeli.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Batal</a>
            <?php else: ?>
                <button type="submit" name="tambah" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Tambah</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Tabel Pembeli -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold mb-4">Daftar Pembeli</h2>
        <table class="w-full border text-sm">
            <thead class="bg-blue-100">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Nama Pembeli</th>
                    <th class="p-2 border">Alamat</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($data_pembeli)): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2 border"><?= $row['id_pembeli'] ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($row['alamat']) ?></td>
                    <td class="p-2 border text-center">
                        <a href="?edit=<?= $row['id_pembeli'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">Edit</a>
                        <a href="?hapus=<?= $row['id_pembeli'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs"
                           onclick="return confirm('Yakin ingin hapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>
</body>
</html>
