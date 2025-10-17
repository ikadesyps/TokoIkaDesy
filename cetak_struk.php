<?php
// cetak_struk.php
// Pastikan skrip ini diakses setelah login (proteksi sesi)

include 'koneksi.php'; 

// Ambil ID transaksi dari URL
if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan!");
}

$id = mysqli_real_escape_string($conn, $_GET['id']); // Amankan input

// Ambil data transaksi + barang + pembeli
$query = mysqli_query($conn, "
    SELECT t.*, b.nama_barang, b.harga, p.nama_pembeli, p.id_pembeli 
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    JOIN pembeli p ON t.id_pembeli = p.id_pembeli
    WHERE t.id_transaksi = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Transaksi tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk - Transaksi #<?= $data['id_transaksi'] ?></title> 
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 280px;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            color: #000;
        }
        h2, h3 {
            text-align: center;
            margin: 5px 0;
        }
        table {
            width: 100%;
            font-size: 13px;
            border-collapse: collapse;
        }
        td {
            padding: 4px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin: 2px 0;
        }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .total {
            border-top: 1px dashed #000;
            margin-top: 5px;
            font-weight: bold;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .footer {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 12px;
            text-align: center;
        }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>

    <h2>📝 Toko Alat Tulis Ika</h2>
    <h3>Struk Pembelian</h3>

    <div class="info-row">
        <span>Tanggal:</span>
        <span><?= date('d/m/Y H:i', strtotime($data['tanggal'])) ?></span>
    </div>
    <div class="info-row">
        <span>ID Transaksi:</span>
        <span>#<?= htmlspecialchars($data['id_transaksi']) ?></span>
    </div>
    <div class="info-row">
        <span>ID Pembeli:</span>
        <span><?= htmlspecialchars($data['id_pembeli']) ?></span>
    </div>
    <div class="info-row">
        <span>Pembeli:</span>
        <strong><?= htmlspecialchars($data['nama_pembeli']) ?></strong>
    </div>
    
    <div class="divider"></div>

    <table>
        <tr>
            <td colspan="2"><?= htmlspecialchars($data['nama_barang']) ?></td>
        </tr>
        <tr>
            <td><?= $data['jumlah'] ?> x @ Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
            <td class="right">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
        </tr>
        
        <tr class="total">
            <td>Total Bayar</td>
            <td class="right">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="footer">
        <p>Terima kasih sudah berbelanja 🙏</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
    </div>

    <div class="center" style="margin-top:10px;">
        <button onclick="window.print()" style="padding:6px 12px; background:#007BFF; color:white; border:none; border-radius:4px;">
            🖨 Cetak
        </button>
    </div>

</body>
</html>