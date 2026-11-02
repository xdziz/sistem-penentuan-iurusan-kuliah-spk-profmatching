<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Ambil top 5 rekomendasi terbaru beserta NIS siswa dari tabel users
$stmt = $conn->prepare("
    SELECT r.jurusan_id, r.ranking, r.nilai_cf, r.nilai_sf, r.nilai_total,
           r.tgl_hitung,
           j.nama_jurusan, j.fakultas, j.universitas, j.akreditasi, j.deskripsi, j.prospek_kerja,
           u.nis, u.nama, u.kelas, u.jurusan_sma
    FROM rekomendasi r
    JOIN jurusan j ON j.id = r.jurusan_id
    JOIN users u ON u.id = r.user_id
    WHERE r.user_id = ?
    ORDER BY r.ranking ASC
    LIMIT 5
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<!-- ── PRINT ONLY HEADER (Tampil saat cetak dokumen) ── -->
<div class="print-header" style="display: none;">
    <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Logo" class="print-logo" style="width: 70px; height: 70px;">
    <div class="print-title" style="text-align: center;">
        <h1 style="font-size: 16pt; margin: 0; font-weight: 800; letter-spacing: 0.5px;">MA DAARUL HIKMAH PAMULANG</h1>
        <h2 style="font-size: 11pt; margin: 2px 0 0 0; font-weight: 600; text-transform: uppercase;">Laporan Hasil Rekomendasi Jurusan Kuliah Siswa</h2>
        <span style="font-size: 8pt; color: #555555; display: block; margin-top: 2px;">Jl. Surya Kencana No.1, Pamulang Barat, Kec. Pamulang, Kota Tangerang Selatan, Banten</span>
    </div>
</div>

<?php if (!empty($hasil)): 
    $first = $hasil[0];
?>
<div class="print-details" style="display: none; margin-bottom: 2rem;">
    <div class="print-details-item" style="padding: 4px 0;"><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($first['nama']); ?></div>
    <div class="print-details-item" style="padding: 4px 0;"><strong>NIS:</strong> <?php echo htmlspecialchars($first['nis']); ?></div>
    <div class="print-details-item" style="padding: 4px 0;"><strong>Kelas / Jurusan SMA:</strong> <?php echo htmlspecialchars($first['kelas'] ?? '-') . ' (' . htmlspecialchars($first['jurusan_sma'] ?? '-') . ')'; ?></div>
    <div class="print-details-item" style="padding: 4px 0;"><strong>Tanggal Perhitungan:</strong> <?php echo tanggal_id($first['tgl_hitung']); ?></div>
</div>
<?php endif; ?>

<!-- ── SCREEN ONLY CONTENT (Header halaman biasa) ── -->
<div style="margin-bottom: 2.5rem;" class="no-print">
    <h1 style="font-size: 2.1rem; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, #6366F1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.25rem;">
        🎓 Hasil Rekomendasi Jurusan
    </h1>
    <p style="color: var(--text-muted); font-size: 1.1rem; margin: 0;">Berdasarkan profil, minat, dan nilai akademik Anda. Berikut Top 5 jurusan yang paling sesuai.</p>
</div>

<?php if (empty($hasil)): ?>
    <div class="card no-print" style="text-align: center; padding: 5rem 2rem;">
        <div style="font-size: 4rem; margin-bottom: 1.5rem; color: var(--text-muted);"><i class="fa-regular fa-folder-open"></i></div>
        <h3 style="margin-bottom: 0.5rem; font-size: 1.4rem;">Belum Ada Hasil Rekomendasi</h3>
        <p style="color: var(--text-muted); max-width: 450px; margin: 0 auto 2rem; font-size: 0.95rem;">Silakan isi data penilaian mandiri terlebih dahulu agar sistem dapat memberikan kalkulasi rekomendasi.</p>
        <a href="isi_data.php" class="btn btn-primary" style="padding: 0.85rem 2.5rem; font-size: 1rem;"><i class="fa-solid fa-pen-to-square"></i> Mulai Penilaian Sekarang</a>
    </div>
<?php else: ?>
    <!-- Recommendation Cards Container -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php foreach ($hasil as $item): 
            $isTop1 = $item['ranking'] == 1;
            // Hitung persentase kecocokan (skala total maksimal adalah 5.0)
            $match_percentage = ($item['nilai_total'] / 5.0) * 100;
            
            // Konfigurasi warna akreditasi
            $akreditasi_class = 'badge-primary';
            if ($item['akreditasi'] === 'A') {
                $akreditasi_class = 'badge-success';
            } elseif ($item['akreditasi'] === 'B') {
                $akreditasi_class = 'badge-primary';
            } else {
                $akreditasi_class = 'badge-warning';
            }
        ?>
            <!-- Satu Kartu Jurusan -->
            <div class="card <?php echo $isTop1 ? '' : 'card-interactive'; ?>" 
                 style="padding: 2rem; <?php echo $isTop1 ? 'border: 2.5px solid var(--primary); background: linear-gradient(135deg, var(--primary-light), rgba(var(--primary-rgb), 0.02)); position: relative;' : ''; ?>">
                
                <?php if ($isTop1): ?>
                    <!-- Glow Tag for Rank #1 -->
                    <div class="no-print" style="position: absolute; top: 1.25rem; right: 1.25rem;">
                        <span class="badge" style="background-color: var(--warning-light); color: var(--warning); border: 1.5px solid rgba(245, 158, 11, 0.3); gap: 0.25rem; font-weight: 800; font-size: 0.75rem;">
                            <i class="fa-solid fa-crown"></i> REKOMENDASI UTAMA
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Grid Tiga Kolom: Peringkat, Deskripsi Jurusan, Kotak Skor -->
                <div style="display: grid; grid-template-columns: 80px 1fr 220px; gap: 2rem; align-items: center;" class="dashboard-grid">
                    
                    <!-- 1. Peringkat / Rank -->
                    <div style="text-align: center;">
                        <div style="font-size: 0.65rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem;">RANK</div>
                        <div style="font-size: <?php echo $isTop1 ? '3rem' : '2.5rem'; ?>; font-weight: 800; line-height: 1; color: <?php echo $isTop1 ? 'var(--primary-dark)' : 'var(--text-dark)'; ?>;">
                            #<?php echo $item['ranking']; ?>
                        </div>
                    </div>

                    <!-- 2. Deskripsi Jurusan & Kampus -->
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.35rem; font-weight: 800; color: var(--primary-dark);">
                            <?php echo htmlspecialchars($item['nama_jurusan']); ?>
                        </h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1rem;">
                            <span style="font-size: 0.9rem; color: var(--text-dark); font-weight: 600;">
                                <i class="fa-solid fa-building-columns" style="margin-right: 0.4rem; color: var(--text-muted);"></i>
                                <?php echo htmlspecialchars($item['universitas']); ?>
                            </span>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
                                <i class="fa-solid fa-graduation-cap" style="margin-right: 0.4rem; color: var(--text-muted);"></i>
                                <?php echo htmlspecialchars($item['fakultas']); ?>
                            </span>
                        </div>
                        
                        <!-- Visual Match Progress Bar -->
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.35rem;">
                                <span style="text-transform: uppercase; letter-spacing: 0.02em;">Kecocokan Profil</span>
                                <span><?php echo number_format($match_percentage, 1); ?>%</span>
                            </div>
                            <div class="match-bar-container">
                                <div class="match-bar-fill" style="width: <?php echo $match_percentage; ?>%; <?php echo $isTop1 ? 'background: linear-gradient(90deg, #F59E0B 0%, #FBBF24 100%);' : ''; ?>"></div>
                            </div>
                        </div>

                        <!-- Badges and Actions -->
                        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;" class="no-print">
                            <span class="badge <?php echo $akreditasi_class; ?>">
                                Akreditasi <?php echo htmlspecialchars($item['akreditasi']); ?>
                            </span>
                            <a href="detail_hasil.php?id=<?php echo $item['jurusan_id']; ?>" 
                               class="btn btn-outline btn-sm" style="font-weight: 700; border-radius: 6px;">
                                <i class="fa-solid fa-circle-info"></i> Detail Jurusan
                            </a>
                        </div>
                    </div>

                    <!-- 3. Kotak Skor Total -->
                    <div style="background: linear-gradient(135deg, var(--primary) 0%, #4F46E5 100%); color: white; padding: 1.5rem; border-radius: var(--radius); text-align: center; box-shadow: var(--shadow-md);">
                        <div style="font-size: 0.7rem; opacity: 0.85; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem;">NILAI TOTAL</div>
                        <div style="font-size: 2.2rem; font-weight: 800; margin: 0; line-height: 1.1; color: #FFFFFF;">
                            <?php echo number_format($item['nilai_total'], 3); ?>
                        </div>
                        <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.5rem; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 0.5rem;">
                            CF: <?php echo number_format($item['nilai_cf'], 2); ?> | 
                            SF: <?php echo number_format($item['nilai_sf'], 2); ?>
                        </div>
                    </div>
                </div>

                <!-- Cetak Deskripsi / Prospek Kerja khusus format Print -->
                <div style="display: none;" class="print-description">
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #CCCCCC; padding-top: 1rem; font-size: 9pt;">
                        <strong>Prospek Kerja:</strong> <?php echo htmlspecialchars($item['prospek_kerja'] ?? '-'); ?>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- Cetak Laporan CTA Button (Screen only) -->
    <div style="margin-top: 3.5rem; text-align: center; padding: 2.5rem; background-color: var(--bg-card); border: 2px dashed var(--border); border-radius: var(--radius);" class="no-print">
        <h4 style="margin: 0 0 0.5rem 0; font-size: 1.15rem; font-weight: 700;">Simpan Hasil Rekomendasi</h4>
        <p style="color: var(--text-muted); margin: 0 0 1.5rem 0; font-size: 0.9rem; max-width: 500px; margin-left: auto; margin-right: auto;">Anda dapat mencetak laporan ini sebagai berkas fisik atau menyimpannya langsung menjadi berkas PDF digital melalui browser.</p>
        <button onclick="window.print()" class="btn btn-primary" style="padding: 0.85rem 2.5rem; font-size: 0.95rem; border-radius: var(--radius-sm);">
            <i class="fa-solid fa-print"></i> Cetak Hasil Rekomendasi
        </button>
    </div>

    <!-- Print Footer (Tampil di bagian bawah berkas cetak) -->
    <div style="display: none; margin-top: 4rem;" class="print-footer-signature">
        <div class="print-signature" style="display: flex; justify-content: space-between;">
            <div class="print-signature-box" style="text-align: center;">
                <span style="font-size: 9pt; display: block; margin-bottom: 5rem;">Mengetahui,<br>Kepala Sekolah MA Darul Hikmah</span>
                <span style="border-top: 1px solid #000000; padding-top: 2px; font-weight: bold; font-size: 10pt; display: inline-block; min-width: 180px;">Siti Aminah, M.Pd.</span>
            </div>
            <div class="print-signature-box" style="text-align: center;">
                <span style="font-size: 9pt; display: block; margin-bottom: 5rem;">Tangerang Selatan, <?php echo tanggal_id(); ?><br>Siswa Bersangkutan</span>
                <span style="border-top: 1px solid #000000; padding-top: 2px; font-weight: bold; font-size: 10pt; display: inline-block; min-width: 180px;"><?php echo htmlspecialchars($first['nama']); ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- CSS Tambahan khusus mode Cetak untuk mencetak elemen tertentu -->
<style>
@media print {
    .print-header,
    .print-details,
    .print-footer-signature {
        display: block !important;
    }
    .print-description {
        display: block !important;
    }
    .dashboard-grid {
        display: block !important;
    }
}
</style>

<?php include '../includes/footer.php'; ?>