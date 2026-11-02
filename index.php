<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Pastikan kelas XII IPA dan XII IPS selalu terdaftar di tabel kelas
$conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPA')");
$conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPS')");

// Cek apakah siswa menumpuk di kelas IPA (XII IPS = 0 dan ada siswa aktif)
$check_total = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetch_row()[0];
$check_ips = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND kelas = 'XII IPS'")->fetch_row()[0];
$check_ipa = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND kelas = 'XII IPA'")->fetch_row()[0];

if ($check_total > 1 && $check_ips === 0 && $check_ipa > 0) {
    // 1. Cek apakah ada data siswa dengan jurusan_sma = 'IPS'
    $ips_res = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND UPPER(jurusan_sma) = 'IPS'");
    $ips_by_major = $ips_res ? (int)$ips_res->fetch_row()[0] : 0;

    if ($ips_by_major > 0) {
        // Sinkronkan kelas dengan jurusan masing-masing
        $conn->query("UPDATE users SET kelas = 'XII IPS' WHERE role = 'siswa' AND UPPER(jurusan_sma) = 'IPS'");
        $conn->query("UPDATE users SET kelas = 'XII IPA' WHERE role = 'siswa' AND (UPPER(jurusan_sma) = 'IPA' OR jurusan_sma IS NULL OR jurusan_sma = '')");
    } else {
        // Jika semua jurusan_sma sama atau belum diisi, bagi rata menjadi dua kelompok (50:50)
        $siswa_query = $conn->query("SELECT id FROM users WHERE role = 'siswa' ORDER BY nis ASC, id ASC");
        $siswa_ids = [];
        while ($r = $siswa_query->fetch_assoc()) {
            $siswa_ids[] = (int)$r['id'];
        }
        $half = (int)ceil(count($siswa_ids) / 2);
        $ipa_ids = array_slice($siswa_ids, 0, $half);
        $ips_ids = array_slice($siswa_ids, $half);

        if (!empty($ipa_ids)) {
            $conn->query("UPDATE users SET kelas = 'XII IPA', jurusan_sma = 'IPA' WHERE id IN (" . implode(',', $ipa_ids) . ")");
        }
        if (!empty($ips_ids)) {
            $conn->query("UPDATE users SET kelas = 'XII IPS', jurusan_sma = 'IPS' WHERE id IN (" . implode(',', $ips_ids) . ")");
        }
    }
}

// Query dengan COUNT untuk menghitung siswa per kelas
$res = $conn->query("
    SELECT k.*, (SELECT COUNT(*) FROM users WHERE kelas = k.nama_kelas AND role = 'siswa') as jumlah_siswa 
    FROM kelas k 
    ORDER BY nama_kelas ASC
");

$kelas_rows = [];
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $kelas_rows[] = $r;
    }
}

include '../../includes/header.php';
?>

<?php if (isset($_GET['status'])): ?>
    <?php
    $alerts = [
        'success_tambah' => ['type' => 'success', 'msg' => 'Kelas baru berhasil ditambahkan!'],
        'error_tambah'   => ['type' => 'danger',  'msg' => 'Gagal menambahkan kelas baru.'],
        'success_edit'   => ['type' => 'success', 'msg' => 'Data kelas berhasil diperbarui!'],
        'error_edit'     => ['type' => 'danger',  'msg' => 'Gagal memperbarui data kelas.'],
        'success_hapus'  => ['type' => 'success', 'msg' => 'Kelas berhasil dihapus!'],
        'error_hapus'    => ['type' => 'danger',  'msg' => 'Gagal menghapus kelas.'],
    ];
    $alert = $alerts[$_GET['status']] ?? null;
    ?>
    <?php if ($alert): ?>
        <div style="background-color: <?= $alert['type'] === 'success' ? 'var(--success-light)' : 'var(--danger-light)' ?>; color: <?= $alert['type'] === 'success' ? 'var(--success)' : 'var(--danger)' ?>; border: 1px solid <?= $alert['type'] === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 600;">
            <i class="fa-solid <?= $alert['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($alert['msg']) ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="card-title" style="font-size: 1.75rem;">Data Kelas</h1>
        <p style="color: var(--text-muted);">Kelola daftar kelas dan pantau jumlah siswa aktif.</p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <button onclick="document.getElementById('modalExport').style.display='flex'" class="btn btn-success" title="Export data siswa ke format Excel / CSV">
            <i class="fa-solid fa-file-excel"></i> Export Siswa ke Excel
        </button>
        <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Kelas
        </button>
    </div>
</div>

<div class="card">
    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                <th style="padding: 1rem 0.5rem; width: 60px;">No</th>
                <th style="padding: 1rem 0.5rem;">Nama Kelas</th>
                <th style="padding: 1rem 0.5rem; text-align: center;">Jumlah Siswa</th>
                <th style="padding: 1rem 0.5rem; text-align: center; width: 260px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($kelas_rows)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada kelas yang terdaftar.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($kelas_rows as $row): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem 0.5rem;"><?php echo $no++; ?></td>
                        <td style="padding: 1rem 0.5rem; font-weight: 600;"><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                        <td style="padding: 1rem 0.5rem; text-align: center;">
                            <a href="../siswa/index.php?kelas=<?php echo urlencode($row['nama_kelas']); ?>" 
                               title="Klik untuk melihat daftar siswa kelas <?php echo htmlspecialchars($row['nama_kelas']); ?>" 
                               style="text-decoration: none;">
                                <span style="background: var(--bg-light); color: var(--primary); padding: 0.3rem 0.85rem; border-radius: 20px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem; border: 1px solid var(--border); transition: var(--transition);">
                                    <i class="fa-solid fa-users" style="font-size: 0.75rem;"></i>
                                    <?php echo $row['jumlah_siswa']; ?> Siswa
                                </span>
                            </a>
                        </td>
                        <td style="padding: 1rem 0.5rem; text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="export_excel.php?kelas=<?php echo urlencode($row['nama_kelas']); ?>" 
                                   class="btn btn-success" 
                                   style="font-size: 0.75rem; padding: 0.4rem 0.65rem;" 
                                   title="Download file Excel untuk siswa kelas <?php echo htmlspecialchars($row['nama_kelas']); ?>">
                                    <i class="fa-solid fa-file-excel"></i> Export Excel
                                </a>
                                <button onclick="editKelas(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_kelas'], ENT_QUOTES); ?>')" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.4rem 0.6rem; background-color: var(--warning); border-color: var(--warning);">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <a href="proses.php?aksi=hapus&id=<?php echo $row['id']; ?>" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.4rem 0.6rem; background-color: var(--danger); border-color: var(--danger);" onclick="return confirm('Hapus kelas ini?')">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Export -->
<div id="modalExport" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="width:100%; max-width:440px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; display:flex; align-items:center; gap:0.5rem; font-size:1.25rem;">
                <i class="fa-solid fa-file-excel" style="color:var(--success);"></i> Export Data Siswa
            </h3>
            <button type="button" onclick="document.getElementById('modalExport').style.display='none'" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer; line-height:1;">&times;</button>
        </div>
        <form action="export_excel.php" method="GET" target="_blank">
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.875rem;">Pilih Kelas</label>
                <select name="kelas" style="width:100%; padding:0.75rem 1rem; border-radius:var(--radius-sm); border:1px solid var(--border); background-color:var(--bg-card); color:var(--text-dark); font-size:0.875rem;">
                    <option value="">-- Semua Kelas (Seluruh Siswa) --</option>
                    <?php foreach ($kelas_rows as $k): ?>
                        <option value="<?php echo htmlspecialchars($k['nama_kelas']); ?>">
                            Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?> (<?php echo $k['jumlah_siswa']; ?> Siswa)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.875rem;">Format File</label>
                <select name="format" style="width:100%; padding:0.75rem 1rem; border-radius:var(--radius-sm); border:1px solid var(--border); background-color:var(--bg-card); color:var(--text-dark); font-size:0.875rem;">
                    <option value="excel">Microsoft Excel Spreadsheet (.xls)</option>
                    <option value="csv">Comma-Separated Values (.csv)</option>
                </select>
            </div>
            <div style="display:flex; gap:1rem;">
                <button type="submit" onclick="setTimeout(function(){document.getElementById('modalExport').style.display='none'}, 500)" class="btn btn-success" style="flex:1;">
                    <i class="fa-solid fa-download"></i> Unduh File
                </button>
                <button type="button" onclick="document.getElementById('modalExport').style.display='none'" class="btn btn-outline" style="flex:1;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="width:100%; max-width:400px;">
        <h3 style="margin-top:0">Tambah Kelas</h3>
        <form action="proses.php" method="POST">
            <input type="hidden" name="aksi" value="tambah">
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas" placeholder="Misal: XII IPA atau XII IPS" required>
            </div>
            <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex:1">Simpan</button>
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" class="btn btn-outline" style="flex:1">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="width:100%; max-width:400px;">
        <h3 style="margin-top:0">Edit Kelas</h3>
        <form action="proses.php" method="POST">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas" id="edit_nama" required>
            </div>
            <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex:1">Update</button>
                <button type="button" onclick="document.getElementById('modalEdit').style.display='none'" class="btn btn-outline" style="flex:1">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function editKelas(id, nama) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('modalEdit').style.display = 'flex';
}
</script>

<?php include '../../includes/footer.php'; ?>
