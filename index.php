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

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 class="card-title" style="font-size: 1.5rem;">Data Jurusan Kuliah</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Kelola daftar jurusan yang tersedia dalam sistem rekomendasi.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary">Tambah Jurusan</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
        <thead>
            <tr style="background-color: var(--bg-light); border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">KODE</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">NAMA JURUSAN</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">UNIVERSITAS</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">AKREDITASI</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary); text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo $row['kode']; ?></td>
                    <td style="padding: 1rem 1.5rem;">
                        <div style="font-weight: 600;"><?php echo $row['nama_jurusan']; ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row['fakultas']; ?></div>
                    </td>
                    <td style="padding: 1rem 1.5rem;"><?php echo $row['universitas']; ?></td>
                    <td style="padding: 1rem 1.5rem;">
                        <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: #EEF2FF; color: var(--primary); font-weight: 700; font-size: 0.75rem;">
                            <?php echo $row['akreditasi']; ?>
                        </span>
                    </td>
                    <td style="padding: 1rem 1.5rem; text-align: center;">
                        <a href="edit.php?id=<?php echo $row['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: 600; margin-right: 1rem;">Edit</a>
                        <a href="../../proses/proses_jurusan.php?aksi=hapus&id=<?php echo $row['id']; ?>" 
                           style="color: var(--danger); text-decoration: none; font-weight: 600;"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">Belum ada data jurusan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
