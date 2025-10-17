// script.js - Versi MPA PHP (login/logout dikelola oleh PHP)

// --- Global Variables untuk Kasir ---
let hargaBarangKasir = 0;
let stokBarangKasir = 0;

// --- Sidebar Toggle (Mobile) ---
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    if(sidebar) sidebar.classList.toggle('sidebar-hidden');
});

// --- Toggle Password (Login Form) ---
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if(passwordIcon) passwordIcon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        if(passwordIcon) passwordIcon.textContent = '👁️';
    }
}

// --- Modal Functions ---
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        const form = modal.querySelector('form');
        if (form) form.reset();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target.id);
    }
});

// --- Kasir: Update Harga & Stok ---
function updateHargaKasir() {
    const barangSelect = document.getElementById('kasir_barang');
    if (!barangSelect) return;

    const selectedOption = barangSelect.options[barangSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        hargaBarangKasir = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        stokBarangKasir = parseInt(selectedOption.getAttribute('data-stok')) || 0;
        const stokInfo = document.getElementById('kasir-stok-info');
        if (stokInfo) stokInfo.textContent = `Stok tersedia: ${stokBarangKasir}`;
        const jumlahInput = document.getElementById('kasir_jumlah');
        if (jumlahInput) jumlahInput.max = stokBarangKasir;
    } else {
        hargaBarangKasir = 0;
        stokBarangKasir = 0;
        const stokInfo = document.getElementById('kasir-stok-info');
        if (stokInfo) stokInfo.textContent = '';
        const jumlahInput = document.getElementById('kasir_jumlah');
        if (jumlahInput) jumlahInput.max = '';
    }
    
    hitungTotalKasir();
}

// --- Kasir: Hitung Total ---
function hitungTotalKasir() {
    const jumlahInput = document.getElementById('kasir_jumlah');
    const totalInput = document.getElementById('kasir_total');
    if (!jumlahInput || !totalInput) return;

    const jumlah = parseInt(jumlahInput.value) || 0;
    const total = hargaBarangKasir * jumlah;
    
    totalInput.value = total > 0 ? `Rp ${total.toLocaleString('id-ID')}` : '';
}

// --- Kasir: Submit Transaksi (Mock, replace with PHP POST) ---
function submitTransaksiKasir(event) {
    event.preventDefault();
    const jumlahInput = document.getElementById('kasir_jumlah');
    const totalInput = document.getElementById('kasir_total');
    const barangSelect = document.getElementById('kasir_barang');
    const pembeliInput = document.getElementById('kasir_pembeli');

    if (!jumlahInput || !totalInput || !barangSelect || !pembeliInput) return;

    const jumlah = parseInt(jumlahInput.value) || 0;

    if (jumlah > stokBarangKasir) {
        alert(`Jumlah tidak boleh melebihi stok yang tersedia (${stokBarangKasir})!`);
        return;
    }

    alert(`Transaksi berhasil disimpan!\nTotal: ${totalInput.value}\nStok barang otomatis berkurang.`);

    // Reset form
    pembeliInput.value = '';
    barangSelect.value = '';
    jumlahInput.value = '';
    totalInput.value = '';
    const stokInfo = document.getElementById('kasir-stok-info');
    if (stokInfo) stokInfo.textContent = '';
}

// --- Utility: Auto-uppercase nama barang ---
document.addEventListener('DOMContentLoaded', function() {
    const namaBarangInput = document.getElementById('nama_barang');
    if (namaBarangInput) {
        namaBarangInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
