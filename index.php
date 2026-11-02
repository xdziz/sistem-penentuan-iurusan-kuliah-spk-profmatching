<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Ambil data kriteria
$query = "SELECT * FROM kriteria ORDER BY aspek ASC, kode ASC";
$result = $conn->query($query);

include '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 class="card-title" style="font-size: 1.5rem;">Kriteria Penilaian</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Kelola kriteria yang digunakan untuk menghitung kecocokan profil.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary">Tambah Kriteria</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
        <thead>
            <tr style="background-color: var(--bg-light); border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">KODE</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">NAMA KRITERIA</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">ASPEK</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">TIPE</th>
                <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary); text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo $row['kode']; ?></td>
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo $row['nama_kriteria']; ?></td>
                    <td style="padding: 1rem 1.5rem; text-transform: capitalize;"><?php echo $row['aspek']; ?></td>
                    <td style="padding: 1rem 1.5rem;">
                        <?php if ($row['tipe'] === 'CF'): ?>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: #DBEAFE; color: #1E40AF; font-weight: 700; font-size: 0.7rem;">CORE FACTOR</span>
                        <?php else: ?>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 4px; background: #F3F4F6; color: #374151; font-weight: 700; font-size: 0.7rem;">SECONDARY FACTOR</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 1rem 1.5rem; text-align: center;">
                        <a href="edit.php?id=<?php echo $row['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: 600; margin-right: 1rem;">Edit</a>
                        <a href="../../proses/proses_kriteria.php?aksi=hapus&id=<?php echo $row['id']; ?>" 
                           style="color: var(--danger); text-decoration: none; font-weight: 600;"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">Belum ada data kriteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
