<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

$id = (int)$_GET['id'];
$query = $conn->prepare("SELECT * FROM jurusan WHERE id = ?");
$query->bind_param('i', $id);
$query->execute();
$jurusan = $query->get_result()->fetch_assoc();

if (!$jurusan) {
    header('Location: index.php');
    exit;
}

// Ambil semua kriteria
$kriteria_list = $conn->query("SELECT * FROM kriteria ORDER BY aspek ASC, kode ASC");

// Ambil data profil ideal yang sudah ada
$profil_existing = [];
$pe = $conn->query("SELECT kriteria_id, nilai_ideal FROM profil_ideal WHERE jurusan_id = $id");
while($row = $pe->fetch_assoc()) {
    $profil_existing[$row['kriteria_id']] = $row['nilai_ideal'];
}

include '../../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <a href="index.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Daftar</a>
    <h1 class="card-title" style="font-size: 1.5rem; margin-top: 1rem;">Atur Profil Ideal: <?php echo $jurusan['nama_jurusan']; ?></h1>
    <p style="color: var(--text-muted); font-size: 0.875rem;">Tentukan nilai target untuk setiap kriteria pada jurusan ini.</p>
</div>

<form action="../../proses/proses_profil.php" method="POST">
    <input type="hidden" name="jurusan_id" value="<?php echo $id; ?>">
    
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="background-color: var(--bg-light); border-bottom: 1px solid var(--border);">
                    <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">KODE</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">ASPEK</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary);">NAMA KRITERIA</th>
                    <th style="padding: 1rem 1.5rem; font-weight: 600; color: var(--secondary); width: 200px;">NILAI IDEAL (1-5)</th>
                </tr>
            </thead>
            <tbody>
                <?php while($k = $kriteria_list->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem 1.5rem;"><?php echo $k['kode']; ?></td>
                    <td style="padding: 1rem 1.5rem; text-transform: capitalize; color: var(--text-muted);"><?php echo $k['aspek']; ?></td>
                    <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo $k['nama_kriteria']; ?></td>
                    <td style="padding: 1rem 1.5rem;">
                        <input type="number" name="nilai[<?php echo $k['id']; ?>]" 
                               value="<?php echo $profil_existing[$k['id']] ?? '3'; ?>" 
                               min="1" max="5" step="1" 
                               style="width: 80px;" required>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
        <a href="index.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Profil Ideal</button>
    </div>
</form>

<?php include '../../includes/footer.php'; ?>
