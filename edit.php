<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'siswa'");
$stmt->bind_param('i', $id);
$stmt->execute();
$siswa = $stmt->get_result()->fetch_assoc();

if (!$siswa) {
    header('Location: index.php');
    exit;
}

include '../../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Daftar</a>
        <h1 class="card-title" style="font-size: 1.75rem; margin-top: 1rem;">Edit Data Siswa</h1>
        <p style="color: var(--text-muted);">Perbarui informasi data diri siswa.</p>
    </div>

    <?php if (isset($_GET['status']) && strpos($_GET['status'], 'error_') === 0): ?>
        <div style="background: #FEF2F2; color: var(--danger); padding: 0.75rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 0.875rem; border: 1px solid #FEE2E2; text-align: center;">
            <?php
            if ($_GET['status'] === 'error_nama_invalid') {
                echo 'Nama lengkap hanya boleh berisi huruf.';
            } elseif ($_GET['status'] === 'error_nis_invalid') {
                echo 'NIS hanya boleh berisi angka.';
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="../../proses/proses_user.php" method="POST">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id" value="<?php echo $siswa['id']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($siswa['nama']); ?>" required
                           pattern="^[A-Za-z\s]+$"
                           title="Nama lengkap hanya boleh berisi huruf"
                           oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                </div>
                <div class="form-group">
                    <label for="nis">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" id="nis" value="<?php echo htmlspecialchars($siswa['nis']); ?>" required
                           pattern="^[0-9]+$"
                           title="NIS hanya boleh berisi angka"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($siswa['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="kelas">Kelas</label>
                    <select name="kelas" id="kelas" required>
                        <?php 
                        $kelas_list = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
                        while($k = $kelas_list->fetch_assoc()): 
                        ?>
                            <option value="<?php echo htmlspecialchars($k['nama_kelas']); ?>" <?php echo $siswa['kelas'] == $k['nama_kelas'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama_kelas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="jurusan_sma">Jurusan SMA</label>
                    <select name="jurusan_sma" id="jurusan_sma" required>
                        <option value="IPA" <?php echo $siswa['jurusan_sma'] == 'IPA' ? 'selected' : ''; ?>>IPA</option>
                        <option value="IPS" <?php echo $siswa['jurusan_sma'] == 'IPS' ? 'selected' : ''; ?>>IPS</option>
                        <option value="Bahasa" <?php echo $siswa['jurusan_sma'] == 'Bahasa' ? 'selected' : ''; ?>>Bahasa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" id="password" placeholder="••••••••">
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="padding-left: 2rem; padding-right: 2rem;">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
