<?php
session_start();
// Hancurkan sesi lama sebelum memulai sesi baru (untuk memastikan sesi bersih)
if (isset($_SESSION['username'])) {
    session_unset();
    session_destroy();
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else if ($data['role'] == 'kasir') {
            header("Location: kasir_dashboard.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Toko Alat Tulis Ika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background:url('bgt.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }
        /* CSS Tambahan untuk Animasi dan Tampilan */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.7s ease-out forwards; }
        
        .card-login {
            border: 1px solid rgba(229, 231, 235, 0.5); /* Garis abu-abu sangat tipis */
            /* Anda bisa menambahkan efek blur atau material design di sini */
        }
        
        /* Simulasikan ikon kue kering dengan ikon pensil yang lebih besar */
        .cookie-icon {
            font-size: 3.5rem; /* Membuat ikon lebih besar */
            color: #1D4ED8;
            margin-bottom: 0.5rem;
            animation: float 3s ease-in-out infinite; /* Gunakan animasi float dari CSS Anda */
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 pt-8 rounded-xl shadow-2xl card-login w-full max-w-sm text-center animate-fadeInUp">
        
        <div class="mb-6">
            <div class="cookie-icon mx-auto" role="img" aria-label="Ikon Alat Tulis">
                 📝
            </div>

            <h1 class="text-2xl font-semibold text-gray-800">Toko Alat Tulis Ika</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Manajemen Toko</p>
        </div>

        <?php if ($error != ''): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4 text-left">
            <div>
                <label class="text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" placeholder="" class="w-full border-b border-gray-300 p-2 focus:border-blue-500 focus:outline-none" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" placeholder="" class="w-full border-b border-gray-300 p-2 focus:border-blue-500 focus:outline-none" required>
            </div>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white p-2 rounded-lg font-semibold hover:bg-blue-700 transition mt-6">
                Masuk
            </button>
        </form>

        <p class="text-xs text-gray-500 mt-5">© <?= date('Y') ?> Ika Desy Pramita Sari</p>
    </div>
</body>
</html>
