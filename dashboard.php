<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
// Cek apakah sudah pernah hitung
$last_rekomendasi = $conn->query("SELECT tgl_hitung FROM rekomendasi WHERE user_id = $user_id ORDER BY tgl_hitung DESC LIMIT 1")->fetch_assoc();

include '../includes/header.php';
?>

<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 2.2rem; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, #6366F1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.25rem;">
        Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>! 👋
    </h1>
    <p style="color: var(--text-muted); font-size: 1.1rem; margin: 0;">Selamat datang di <strong>Sistem Rekomendasi Jurusan Kuliah</strong> MA Darul Hikmah</p>
</div>

<!-- Main Dashboard Grid Layout -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;" class="dashboard-grid">

    <!-- ==================== COLUMN LEFT: Hero & Features ==================== -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">

        <!-- Hero Card Call to Action -->
        <div class="card" style="background: linear-gradient(135deg, var(--primary) 0%, #4F46E5 100%); color: white; border: none; padding: 3rem; position: relative; overflow: hidden; box-shadow: var(--shadow-lg);">
            <!-- Decorative Background Icons -->
            <div style="position: absolute; top: -50px; right: -30px; font-size: 11rem; opacity: 0.12; color: #FFFFFF;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div style="position: absolute; bottom: -30px; left: 10px; font-size: 6rem; opacity: 0.08; color: #FFFFFF;">
                <i class="fa-solid fa-book-open"></i>
            </div>
            
            <div style="position: relative; z-index: 1;">
                <h2 style="margin: 0 0 1rem 0; font-size: 2.2rem; line-height: 1.2; color: #FFFFFF; font-weight: 800;">
                    Siap Menentukan<br>Masa Depanmu?
                </h2>
                <p style="font-size: 1.1rem; opacity: 0.95; max-width: 85%; margin-bottom: 2rem; color: rgba(255, 255, 255, 0.9);">
                    Temukan jurusan kuliah yang paling sesuai dengan bakat, minat, nilai akademik, dan kepribadianmu menggunakan algoritma Profile Matching.
                </p>
                <a href="isi_data.php" class="btn" style="background: white; color: var(--primary); font-weight: 800; padding: 0.85rem 2rem; border-radius: var(--radius-sm); border: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fa-solid fa-rocket"></i> Mulai Penilaian Sekarang
                </a>
            </div>
        </div>

        <!-- Features Info Cards -->
        <div>
            <h3 style="margin-bottom: 1.25rem; font-size: 1.25rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-nodes"></i> Mengapa Menggunakan Sistem Ini?
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
                <div class="card card-interactive" style="border-top: 4px solid var(--primary); padding: 1.5rem; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem; color: var(--primary);">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h4 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; font-weight: 700;">Data-Driven</h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0; line-height: 1.5;">Perhitungan akurat menggabungkan nilai rapor akademik, minat bakat, dan kepribadian.</p>
                </div>
                
                <div class="card card-interactive" style="border-top: 4px solid #8B5CF6; padding: 1.5rem; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem; color: #8B5CF6;">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h4 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; font-weight: 700;">Personal & Akurat</h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0; line-height: 1.5;">Hasil dicocokkan khusus dengan membandingkan kompetensi diri dengan profil ideal jurusan.</p>
                </div>
                
                <div class="card card-interactive" style="border-top: 4px solid #10B981; padding: 1.5rem; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem; color: #10B981;">
                        <i class="fa-solid fa-school"></i>
                    </div>
                    <h4 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; font-weight: 700;">Jurusan Terbaik</h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0; line-height: 1.5;">Rekomendasi terpilih mencakup universitas terakreditasi dengan prospek kerja tinggi.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- ==================== COLUMN RIGHT: Sidebar Widgets ==================== -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Status Card -->
        <div class="card">
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                <i class="fa-solid fa-circle-info"></i> Status Penilaian
            </h3>
            
            <?php if ($last_rekomendasi): ?>
                <div style="padding: 1.25rem; background-color: var(--success-light); border-radius: var(--radius-sm); border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="font-size: 1.5rem; color: var(--success);"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div style="font-size: 0.7rem; color: var(--success); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Sudah Dihitung</div>
                        <div style="font-weight: 700; font-size: 1rem; color: var(--text-dark);">
                            <?php echo date('d M Y', strtotime($last_rekomendasi['tgl_hitung'])); ?>
                        </div>
                    </div>
                </div>
                <a href="hasil.php" class="btn btn-outline" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fa-solid fa-award"></i> Lihat Hasil Terakhir
                </a>
            <?php else: ?>
                <div style="padding: 1.25rem; background-color: var(--warning-light); border-radius: var(--radius-sm); border: 1px solid rgba(245, 158, 11, 0.2); display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="font-size: 1.5rem; color: var(--warning);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <div style="font-size: 0.7rem; color: var(--warning); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Belum Ada Data</div>
                        <div style="font-size: 0.85rem; color: var(--text-dark); font-weight: 600;">Ayo mulai isi penilaian mandiri Anda!</div>
                    </div>
                </div>
                <a href="isi_data.php" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fa-solid fa-circle-play"></i> Mulai Sekarang
                </a>
            <?php endif; ?>
        </div>

        <!-- Guide Steps Widget -->
        <div class="card">
            <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                <i class="fa-solid fa-route"></i> Alur Langkah
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem; border-radius: var(--radius-sm); background-color: rgba(var(--primary-rgb), 0.02); border: 1px dashed var(--border);">
                    <div style="background-color: var(--primary-light); color: var(--primary); font-weight: 800; width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; font-size: 0.85rem;">1</div>
                    <div>
                        <strong style="font-size: 0.875rem; display: block; margin-bottom: 0.15rem;">Isi Data Diri</strong>
                        <span style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; display: block;">Masukkan nilai rata-rata rapor, kuesioner minat bakat, dan profil kepribadian.</span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem; border-radius: var(--radius-sm); background-color: rgba(var(--primary-rgb), 0.02); border: 1px dashed var(--border);">
                    <div style="background-color: var(--primary-light); color: var(--primary); font-weight: 800; width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; font-size: 0.85rem;">2</div>
                    <div>
                        <strong style="font-size: 0.875rem; display: block; margin-bottom: 0.15rem;">Lihat Rekomendasi</strong>
                        <span style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; display: block;">Dapatkan ranking Top 5 rekomendasi jurusan kuliah terakreditasi beserta prospek kerjanya.</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>