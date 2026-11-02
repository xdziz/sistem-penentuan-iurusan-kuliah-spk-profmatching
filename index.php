<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Ambil data jurusan
$query = "SELECT * FROM jurusan ORDER BY kode ASC";
$result = $conn->query($query);

include '../../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <h1 class="card-title" style="font-size: 1.5rem;">Profil Ideal Jurusan</h1>
    <p style="color: var(--text-muted); font-size: 0.875rem;">Atur nilai standar (1-5) untuk setiap kriteria pada masing-masing jurusan.</p>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
        <thead>
            <tr style="background-color: var(--bg-light); border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">KODE</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">NAMA JURUSAN</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">STATUS PROFIL</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary); text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_kriteria = $conn->query("SELECT COUNT(*) FROM kriteria")->fetch_row()[0];
            
            if ($result->num_rows > 0): 
                while($row = $result->fetch_assoc()): 
                    // Cek berapa kriteria yang sudah diisi profil idealnya
                    $jid = $row['id'];
                    $filled = $conn->query("SELECT COUNT(*) FROM profil_ideal WHERE jurusan_id = $jid")->fetch_row()[0];
            ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo $row['kode']; ?></td>
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo $row['nama_jurusan']; ?></td>
                    <td style="padding: 1rem 1.5rem;">
                        <?php if ($filled >= $total_kriteria && $total_kriteria > 0): ?>
                            <span style="color: var(--success); font-weight: 600;">Lengkap (<?php echo $filled; ?>/<?php echo $total_kriteria; ?>)</span>
                        <?php else: ?>
                            <span style="color: var(--warning); font-weight: 600;">Belum Lengkap (<?php echo $filled; ?>/<?php echo $total_kriteria; ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 1rem 1.5rem; text-align: center;">
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-outline" style="font-size: 0.75rem;">Atur Profil Ideal</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-muted);">Belum ada data jurusan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
