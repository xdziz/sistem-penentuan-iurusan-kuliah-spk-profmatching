<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

include '../../includes/header.php';
?>

<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Daftar</a>
        <h1 class="card-title" style="font-size: 1.5rem; margin-top: 1rem;">Tambah Kriteria Penilaian</h1>
    </div>

    <div class="card">
        <form action="../../proses/proses_kriteria.php?aksi=tambah" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="kode">Kode Kriteria</label>
                    <input type="text" name="kode" id="kode" placeholder="K01" required>
                </div>
                <div class="form-group">
                    <label for="nama_kriteria">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" id="nama_kriteria" placeholder="Nilai Matematika" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="aspek">Aspek Penilaian</label>
                    <select name="aspek" id="aspek">
                        <option value="akademik">Akademik</option>
                        <option value="minat">Minat</option>
                        <option value="bakat">Bakat</option>
                        <option value="kepribadian">Kepribadian</option>
                        <option value="ekonomi">Ekonomi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipe">Tipe Faktor</label>
                    <select name="tipe" id="tipe">
                        <option value="CF">Core Factor (Utama)</option>
                        <option value="SF">Secondary Factor (Pendukung)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" placeholder="Penjelasan mengenai kriteria ini..."></textarea>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="index.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Kriteria</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
