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

include '../../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Daftar</a>
        <h1 class="card-title" style="font-size: 1.5rem; margin-top: 1rem;">Edit Jurusan: <?php echo $jurusan['nama_jurusan']; ?></h1>
    </div>

    <div class="card">
        <form action="../../proses/proses_jurusan.php?aksi=edit" method="POST">
            <input type="hidden" name="id" value="<?php echo $jurusan['id']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="kode">Kode Jurusan</label>
                    <input type="text" name="kode" id="kode" value="<?php echo $jurusan['kode']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_jurusan">Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" id="nama_jurusan" value="<?php echo $jurusan['nama_jurusan']; ?>" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="fakultas">Fakultas</label>
                    <input type="text" name="fakultas" id="fakultas" value="<?php echo $jurusan['fakultas']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="universitas">Universitas</label>
                    <input type="text" name="universitas" id="universitas" value="<?php echo $jurusan['universitas']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="akreditasi">Akreditasi</label>
                <select name="akreditasi" id="akreditasi">
                    <option value="A" <?php echo $jurusan['akreditasi'] == 'A' ? 'selected' : ''; ?>>Terakreditasi A</option>
                    <option value="B" <?php echo $jurusan['akreditasi'] == 'B' ? 'selected' : ''; ?>>Terakreditasi B</option>
                    <option value="C" <?php echo $jurusan['akreditasi'] == 'C' ? 'selected' : ''; ?>>Terakreditasi C</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"><?php echo $jurusan['deskripsi']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="prospek_kerja">Prospek Kerja</label>
                <textarea name="prospek_kerja" id="prospek_kerja" rows="3"><?php echo $jurusan['prospek_kerja']; ?></textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="index.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
