<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

$id = (int)$_GET['id'];
$query = $conn->prepare("SELECT * FROM kriteria WHERE id = ?");
$query->bind_param('i', $id);
$query->execute();
$kriteria = $query->get_result()->fetch_assoc();

if (!$kriteria) {
    header('Location: index.php');
    exit;
}

include '../../includes/header.php';
?>

<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Daftar</a>
        <h1 class="card-title" style="font-size: 1.5rem; margin-top: 1rem;">Edit Kriteria: <?php echo $kriteria['nama_kriteria']; ?></h1>
    </div>

    <div class="card">
        <form action="../../proses/proses_kriteria.php?aksi=edit" method="POST">
            <input type="hidden" name="id" value="<?php echo $kriteria['id']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="kode">Kode Kriteria</label>
                    <input type="text" name="kode" id="kode" value="<?php echo $kriteria['kode']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_kriteria">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" id="nama_kriteria" value="<?php echo $kriteria['nama_kriteria']; ?>" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="aspek">Aspek Penilaian</label>
                    <select name="aspek" id="aspek">
                        <option value="akademik" <?php echo $kriteria['aspek'] == 'akademik' ? 'selected' : ''; ?>>Akademik</option>
                        <option value="minat" <?php echo $kriteria['aspek'] == 'minat' ? 'selected' : ''; ?>>Minat</option>
                        <option value="bakat" <?php echo $kriteria['aspek'] == 'bakat' ? 'selected' : ''; ?>>Bakat</option>
                        <option value="kepribadian" <?php echo $kriteria['aspek'] == 'kepribadian' ? 'selected' : ''; ?>>Kepribadian</option>
                        <option value="ekonomi" <?php echo $kriteria['aspek'] == 'ekonomi' ? 'selected' : ''; ?>>Ekonomi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipe">Tipe Faktor</label>
                    <select name="tipe" id="tipe">
                        <option value="CF" <?php echo $kriteria['tipe'] == 'CF' ? 'selected' : ''; ?>>Core Factor (Utama)</option>
                        <option value="SF" <?php echo $kriteria['tipe'] == 'SF' ? 'selected' : ''; ?>>Secondary Factor (Pendukung)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3"><?php echo $kriteria['keterangan']; ?></textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="index.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
