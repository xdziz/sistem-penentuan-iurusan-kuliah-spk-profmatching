<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>Temukan Jurusan Impianmu.</h1>
        <p>Gunakan Metode <strong>Profile Matching</strong> untuk mendapatkan rekomendasi jurusan kuliah yang paling sesuai dengan bakat, minat, dan kemampuan akademikmu.</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="<?php echo BASE_URL; ?>user/login.php" class="btn btn-primary">Mulai Sekarang</a>
            <a href="#tentang" class="btn btn-outline">Pelajari Lebih Lanjut</a>
        </div>
    </div>
</section>

<section id="tentang" style="padding: 4rem 0; background: var(--white);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <h2 style="font-size: 2rem; font-weight: 700;">Kenapa Menggunakan Sistem Ini?</h2>
                <p style="color: var(--secondary); margin-bottom: 2rem;">Sistem ini dirancang khusus untuk siswa MA Daarul Hikmah Pamulang menggunakan algoritma Profile Matching yang akurat dalam memetakan potensi siswa terhadap kriteria yang dibutuhkan setiap jurusan kuliah.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--success);">✔</span> Analisis Akademik & Minat
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--success);">✔</span> Perhitungan Berbasis Bobot Gap
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--success);">✔</span> Rekomendasi Top 5 Jurusan
                    </li>
                </ul>
            </div>
            <div style="background: var(--bg-light); padding: 2rem; border-radius: var(--radius);">
                <div class="card" style="border-style: dashed;">
                    <h3 style="margin-top:0">Statistik Sistem</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">SISWA TERDAFTAR</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">120+</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">JURUSAN KULIAH</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">50+</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
