<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Ambil riwayat perhitungan (dikelompokkan berdasarkan tanggal)
$query = "
    SELECT DATE(tgl_hitung) as tanggal, COUNT(*) as jumlah_jurusan, MAX(nilai_total) as nilai_tertinggi
    FROM rekomendasi
    WHERE user_id = $user_id
    GROUP BY DATE(tgl_hitung)
    ORDER BY tanggal DESC
";
$riwayat = $conn->query($query);

include '../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <h1 class="card-title" style="font-size: 1.5rem;">Riwayat Penilaian</h1>
    <p style="color: var(--text-muted); font-size: 0.875rem;">Daftar riwayat perhitungan rekomendasi yang pernah Anda lakukan.</p>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
        <thead>
            <tr style="background-color: var(--bg-light); border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">TANGGAL</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">JUMLAH JURUSAN</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">SKOR TERTINGGI</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary); text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($riwayat->num_rows > 0): ?>
                <?php while($row = $riwayat->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo tanggal_id($row['tanggal']); ?></td>
                    <td style="padding: 1rem 1.5rem;"><?php echo $row['jumlah_jurusan']; ?> Jurusan</td>
                    <td style="padding: 1rem 1.5rem; font-weight: 700; color: var(--primary);"><?php echo number_format($row['nilai_tertinggi'], 3); ?></td>
                    <td style="padding: 1rem 1.5rem; text-align: center;">
                        <a href="hasil.php" class="btn btn-outline" style="font-size: 0.75rem;">Lihat Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-muted);">Belum ada riwayat penilaian.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
