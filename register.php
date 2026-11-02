<?php
session_start();
require_once '../config/database.php';
include '../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
    }

    .register-wrapper {
        max-width: 640px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .custom-card {
        background: var(--bg-card);
        border-radius: 24px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .brand-banner {
        background: linear-gradient(135deg, #4f46e5, #06b6d4);
        padding: 2.8rem 2rem;
        text-align: center;
        color: #ffffff;
    }

    .brand-banner h2,
    .brand-banner p {
        color: #ffffff !important;
    }

    .brand-banner i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #ffffff !important;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }

    .form-container {
        padding: 2.8rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 480px) {
        .form-grid { grid-template-columns: 1fr; }
    }

    .input-group {
        margin-bottom: 1.4rem;
        position: relative;
    }

    .input-group label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        z-index: 10;
    }

    .input-wrapper input,
    .input-wrapper select {
        width: 100%;
        padding: 0.9rem 1rem 0.9rem 2.8rem;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: var(--bg-light);
        color: var(--text-dark);
    }

    .input-wrapper input:focus,
    .input-wrapper select:focus {
        border-color: var(--primary);
        background-color: var(--bg-card);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .error-message {
        color: #ef4444;
        font-size: 0.82rem;
        margin-top: 0.4rem;
        display: none;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary), var(--primary-hover));
        color: white;
        border: none;
        border-radius: 12px;
        padding: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
        width: 100%;
        margin-top: 1.8rem;
        cursor: pointer;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
    }
</style>

<div class="register-wrapper">
    <div class="custom-card">
        <div class="brand-banner">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2 style="font-size: 1.75rem; font-weight: 700; margin: 0 0 0.5rem 0;">Temukan Masa Depanmu</h2>
            <p style="margin: 0; opacity: 0.95;">Lengkapi data diri untuk rekomendasi jurusan kuliah</p>
        </div>

        <div class="form-container">
            <form id="registerForm" action="../proses/proses_register.php" method="POST">
                
                <div class="form-grid">
                    <!-- Nama Lengkap -->
                    <div class="input-group">
                        <label for="nama">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <input type="text" name="nama" id="nama" 
                                   placeholder="Ahmad Fauzi" 
                                   maxlength="25"
                                   onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || 
                                                      (event.charCode >= 97 && event.charCode <= 122) || 
                                                      event.charCode === 32"
                                   required>
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <small class="error-message" id="namaError">Nama hanya boleh huruf dan spasi (maksimal 25 karakter)</small>
                    </div>

                    <!-- NIS -->
                    <div class="input-group">
                        <label for="nis">NIS (Nomor Induk Siswa)</label>
                        <div class="input-wrapper">
                            <input type="text" name="nis" id="nis" 
                                   placeholder="12345678" 
                                   maxlength="12"
                                   pattern="\d+"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   required>
                            <i class="fa-regular fa-id-card"></i>
                        </div>
                        <small class="error-message" id="nisError">Hanya boleh angka (maksimal 12 digit)</small>
                    </div>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" 
                               placeholder="siswa@darulhikmah.com" 
                               maxlength="100" required>
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <small class="error-message" id="emailError">Masukkan email yang valid</small>
                </div>

                <!-- Password & Konfirmasi -->
                <div class="form-grid">
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" 
                                   placeholder="Minimal 8 karakter" 
                                   minlength="8" maxlength="32" required>
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <small class="error-message" id="passError">Password minimal 8 & maksimal 32 karakter</small>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   placeholder="Ulangi password" 
                                   maxlength="32" required>
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <small class="error-message" id="confirmError">Password tidak cocok</small>
                    </div>
                </div>

                <!-- Kelas & Jurusan -->
                <div class="form-grid">
                    <div class="input-group">
                        <label for="kelas">Kelas</label>
                        <div class="input-wrapper">
                            <select name="kelas" id="kelas" required onchange="syncJurusanSma(this.value)">
                                <option value="">Pilih Kelas</option>
                                <?php 
                                $k_res = $conn->query("SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");
                                if ($k_res && $k_res->num_rows > 0) {
                                    while($kr = $k_res->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($kr['nama_kelas']) . '">' . htmlspecialchars($kr['nama_kelas']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="XII IPA">XII IPA</option>';
                                    echo '<option value="XII IPS">XII IPS</option>';
                                }
                                ?>
                            </select>
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <i class="fa-solid fa-chevron-down" style="left:auto; right:1rem; color:#94a3b8;"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="jurusan_sma">Jurusan Saat Ini</label>
                        <div class="input-wrapper">
                            <select name="jurusan_sma" id="jurusan_sma" required>
                                <option value="">Pilih Jurusan</option>
                                <option value="IPA">IPA</option>
                                <option value="IPS">IPS</option>
                                <option value="Bahasa">Bahasa</option>
                            </select>
                            <i class="fa-solid fa-book-open"></i>
                            <i class="fa-solid fa-chevron-down" style="left:auto; right:1rem; color:#94a3b8;"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Mulai Rekomendasi Jurusan
                </button>
            </form>

            <div class="form-footer mt-6 text-center">
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    Sudah punya akun? 
                    <a href="login.php" style="color: var(--primary); font-weight: 600;">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Validasi Form Submit
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let valid = true;

    const nama = document.getElementById('nama').value.trim();
    if (nama.length > 25 || !/^[A-Za-z\s]+$/.test(nama)) {
        document.getElementById('namaError').style.display = 'block';
        valid = false;
    }

    const nis = document.getElementById('nis').value;
    if (!/^\d+$/.test(nis) || nis.length > 12) {
        document.getElementById('nisError').style.display = 'block';
        valid = false;
    }

    const pass = document.getElementById('password').value;
    if (pass.length < 8 || pass.length > 32) {
        document.getElementById('passError').style.display = 'block';
        valid = false;
    }

    const confirmPass = document.getElementById('confirm_password').value;
    if (pass !== confirmPass) {
        document.getElementById('confirmError').style.display = 'block';
        valid = false;
    }

    if (!valid) e.preventDefault();
});

// Sembunyikan error saat mengetik
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        const errorId = this.id + 'Error';
        const errorEl = document.getElementById(errorId);
        if (errorEl) errorEl.style.display = 'none';
    });
});

function syncJurusanSma(val) {
    const jurSelect = document.getElementById('jurusan_sma');
    if (!jurSelect) return;
    if (val.toUpperCase().includes('IPA')) {
        jurSelect.value = 'IPA';
    } else if (val.toUpperCase().includes('IPS')) {
        jurSelect.value = 'IPS';
    } else if (val.toUpperCase().includes('BAHASA')) {
        jurSelect.value = 'Bahasa';
    }
}
</script>

<?php include '../includes/footer.php'; ?>