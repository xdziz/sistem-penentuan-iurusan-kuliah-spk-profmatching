<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Generate Kode Otomatis (K-01, K-02, dst)
$query_latest = $conn->query("SELECT kode FROM jurusan ORDER BY id DESC LIMIT 1");
if ($query_latest->num_rows > 0) {
    $latest = $query_latest->fetch_assoc();
    $last_number = (int) substr($latest['kode'], 2);
    $new_number = $last_number + 1;
} else {
    $new_number = 1;
}
$auto_kode = "K-" . str_pad($new_number, 2, '0', STR_PAD_LEFT);

include '../../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Daftar</a>
        <h1 class="card-title" style="font-size: 1.5rem; margin-top: 1rem;">Tambah Jurusan Baru</h1>
    </div>

    <div class="card">
        <form action="../../proses/proses_jurusan.php?aksi=tambah" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="kode">Kode Jurusan (Otomatis)</label>
                    <input type="text" name="kode" id="kode" value="<?php echo $auto_kode; ?>" readonly style="background-color: var(--bg-light); cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label for="nama_jurusan">Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" id="nama_jurusan" placeholder="Teknik Informatika" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="fakultas">Fakultas</label>
                    <input type="text" name="fakultas" id="fakultas" placeholder="Teknik" required>
                </div>
                <div class="form-group">
                    <label for="universitas">Universitas</label>
                    <input type="text" name="universitas" id="universitas" placeholder="Universitas Indonesia" required>
                </div>
            </div>

            <div class="form-group">
                <label for="akreditasi">Akreditasi</label>
                <select name="akreditasi" id="akreditasi">
                    <option value="A">Terakreditasi A</option>
                    <option value="B" selected>Terakreditasi B</option>
                    <option value="C">Terakreditasi C</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai jurusan ini..."></textarea>
            </div>

            <div class="form-group">
                <label for="prospek_kerja">Prospek Kerja</label>
                <textarea name="prospek_kerja" id="prospek_kerja" rows="3" placeholder="Contoh: Software Engineer, Data Scientist..."></textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="index.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Jurusan</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
