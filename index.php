<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

// Ambil statistik sederhana
$total_siswa = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetch_row()[0];
$total_jurusan = $conn->query("SELECT COUNT(*) FROM jurusan")->fetch_row()[0];
$total_kriteria = $conn->query("SELECT COUNT(*) FROM kriteria")->fetch_row()[0];

include '../includes/header.php';
?>

<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 2.1rem; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, #6366F1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.25rem;">
        Dashboard Admin
    </h1>
    <p style="color: var(--text-muted); font-size: 1.1rem; margin: 0;">Selamat datang kembali, Administrator. Kelola konfigurasi dan data sistem rekomendasi di sini.</p>
</div>

<!-- Stats Grid Widgets -->
<div class="stats-grid" style="margin-bottom: 3rem;">
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Total Siswa</span>
            <span class="stat-value"><?php echo $total_siswa; ?></span>
        </div>
        <div class="stat-icon" style="background-color: var(--primary-light); color: var(--primary);">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Jurusan Kuliah</span>
            <span class="stat-value"><?php echo $total_jurusan; ?></span>
        </div>
        <div class="stat-icon" style="background-color: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <span class="stat-label">Kriteria Penilaian</span>
            <span class="stat-value"><?php echo $total_kriteria; ?></span>
        </div>
        <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.12); color: #10B981;">
            <i class="fa-solid fa-sliders"></i>
        </div>
    </div>
</div>

<!-- Modul Manajemen Section -->
<h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--primary);">
    <i class="fa-solid fa-cube"></i> Modul Manajemen Sistem
</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- 1. Data Siswa -->
    <div class="card card-interactive" style="text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
        <div>
            <div style="font-size: 2.25rem; margin-bottom: 1rem; color: var(--primary);">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700;">Data Siswa</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;">Kelola data profil siswa kelas XII, tambahkan siswa baru, atau edit info kelas dan jurusan SMA.</p>
        </div>
        <a href="siswa/index.php" class="btn btn-outline" style="width: 100%;">
            <i class="fa-solid fa-user-tag"></i> Kelola Siswa
        </a>
    </div>

    <!-- 2. Data Jurusan -->
    <div class="card card-interactive" style="text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
        <div>
            <div style="font-size: 2.25rem; margin-bottom: 1rem; color: #8B5CF6;">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700;">Data Jurusan</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;">Kelola daftar jurusan kuliah universitas, deskripsi program, akreditasi, serta prospek kerja.</p>
        </div>
        <a href="jurusan/index.php" class="btn btn-outline" style="width: 100%;">
            <i class="fa-solid fa-school-flag"></i> Kelola Jurusan
        </a>
    </div>

    <!-- 3. Kriteria & Bobot -->
    <div class="card card-interactive" style="text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
        <div>
            <div style="font-size: 2.25rem; margin-bottom: 1rem; color: #10B981;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700;">Kriteria & Bobot</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;">Konfigurasi kriteria Core Factor (CF) & Secondary Factor (SF) beserta bobot nilai gap.</p>
        </div>
        <a href="kriteria/index.php" class="btn btn-outline" style="width: 100%;">
            <i class="fa-solid fa-gears"></i> Kelola Kriteria
        </a>
    </div>

    <!-- 4. Profil Ideal -->
    <div class="card card-interactive" style="text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
        <div>
            <div style="font-size: 2.25rem; margin-bottom: 1rem; color: #F59E0B;">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700;">Profil Ideal</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;">Tentukan nilai kompetensi standar ideal untuk masing-masing kriteria penilaian per jurusan kuliah.</p>
        </div>
        <a href="profil_ideal/index.php" class="btn btn-outline" style="width: 100%;">
            <i class="fa-solid fa-crosshairs"></i> Kelola Profil
        </a>
    </div>

    <!-- 5. Laporan Rekapitulasi -->
    <div class="card card-interactive" style="text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
        <div>
            <div style="font-size: 2.25rem; margin-bottom: 1rem; color: #EF4444;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700;">Laporan Sistem</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;">Pantau seluruh riwayat dan rekap hasil rekomendasi seluruh siswa MA Darul Hikmah Pamulang.</p>
        </div>
        <a href="../laporan/index.php" class="btn btn-outline" style="width: 100%;">
            <i class="fa-solid fa-receipt"></i> Lihat Laporan
        </a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
