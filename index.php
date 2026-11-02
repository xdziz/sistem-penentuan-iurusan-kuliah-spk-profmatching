<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Ambil filter kelas
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';

// Ambil daftar kelas dengan jumlah siswa
$kelas_query = $conn->query("
    SELECT k.nama_kelas, 
           (SELECT COUNT(*) FROM users WHERE kelas = k.nama_kelas AND role = 'siswa') as jml 
    FROM kelas k 
    ORDER BY nama_kelas ASC
");
$kelas_list = $kelas_query->fetch_all(MYSQLI_ASSOC);

// Ambil daftar siswa
$query_siswa = "SELECT id, nama, nis, email, kelas, jurusan_sma FROM users WHERE role = 'siswa'";
if ($filter_kelas) {
    $query_siswa .= " AND kelas = '" . $conn->real_escape_string($filter_kelas) . "'";
}
$query_siswa .= " ORDER BY kelas ASC, nama ASC";
$siswa_res = $conn->query($query_siswa);

// Kelompokkan siswa berdasarkan kelas
$siswa_per_kelas = [];
while ($row = $siswa_res->fetch_assoc()) {
    $kelas_nama = $row['kelas'] ?: 'Tanpa Kelas';
    $siswa_per_kelas[$kelas_nama][] = $row;
}

include '../../includes/header.php';
?>

<!-- Page Header -->
<div style="background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%); color: white; padding: 3rem 2rem; border-radius: 20px; margin-bottom: 2.5rem; position: relative; overflow: hidden; box-shadow: var(--shadow-lg);">
    <div style="position: absolute; top: -50px; right: -40px; font-size: 200px; opacity: 0.08; color: #FFFFFF;">
        <i class="fa-solid fa-users"></i>
    </div>
    <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 0.6rem 0; color: #FFFFFF;">Data Siswa</h1>
    <p style="font-size: 1.15rem; opacity: 0.92; max-width: 680px; color: rgba(255, 255, 255, 0.9);">Kelola data siswa dan pantau hasil rekomendasi jurusan mereka secara efisien.</p>
</div>

<!-- Action Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2.8rem;">
    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <a href="tambah.php" class="btn btn-primary" style="padding: 0.9rem 1.8rem; border-radius: 14px;">
            <i class="fa-solid fa-user-plus"></i> Tambah Siswa Baru
        </a>
        <a href="../kelas/export_excel.php<?= $filter_kelas ? '?kelas=' . urlencode($filter_kelas) : '' ?>" class="btn btn-success" style="padding: 0.9rem 1.6rem; border-radius: 14px;" title="Export data siswa ke Excel">
            <i class="fa-solid fa-file-excel"></i> Export <?= $filter_kelas ? 'Kelas ' . htmlspecialchars($filter_kelas) : 'Semua Siswa' ?> ke Excel
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" style="display: flex; align-items: center; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.8rem;">
            <label style="font-weight: 700; color: var(--text-dark); margin: 0;">Filter Kelas:</label>
            <select name="kelas" onchange="this.form.submit()" 
                    style="padding: 0.85rem 1.3rem; border-radius: 14px; border: 1px solid var(--border); background-color: var(--bg-card); color: var(--text-dark); font-size: 1rem; min-width: 240px; box-shadow: var(--shadow-sm);">
                <option value="">Semua Kelas</option>
                <?php foreach($kelas_list as $k): ?>
                    <option value="<?= htmlspecialchars($k['nama_kelas']) ?>" <?= $filter_kelas == $k['nama_kelas'] ? 'selected' : '' ?>>
                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?> (<?= $k['jml'] ?> siswa)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if (empty($siswa_per_kelas)): ?>
    <div class="card" style="text-align: center; padding: 6rem 2rem;">
        <div style="font-size: 5rem; margin-bottom: 1rem; opacity: 0.7; color: var(--text-muted);">
            <i class="fa-regular fa-folder-open"></i>
        </div>
        <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">Belum ada data siswa</h3>
        <p style="color: var(--text-muted); margin: 0;">Silakan tambahkan siswa baru untuk memulai.</p>
    </div>
<?php else: ?>
    <?php foreach ($siswa_per_kelas as $kelas_nama => $daftar_siswa): ?>
        <div style="margin-bottom: 3.5rem;">
            <!-- Kelas Header -->
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.3rem;">
                <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0; color: var(--text-dark);">
                    Kelas <?= htmlspecialchars($kelas_nama) ?>
                </h2>
                <div style="flex: 1; height: 3px; background: linear-gradient(to right, var(--border), transparent); border-radius: 3px;"></div>
                <span style="background: var(--bg-light); color: var(--text-muted); padding: 0.45rem 1.2rem; border-radius: 9999px; font-size: 0.95rem; font-weight: 600; box-shadow: var(--shadow-sm);">
                    <?= count($daftar_siswa) ?> Siswa
                </span>
            </div>

            <!-- Table Container -->
            <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-md);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.96rem;">
                        <thead>
                            <tr style="background-color: rgba(var(--primary-rgb), 0.04); border-bottom: 2px solid var(--border);">
                                <th style="padding: 1.4rem 1.2rem; text-align: left; font-weight: 700; color: var(--text-dark); width: 130px;">NIS</th>
                                <th style="padding: 1.4rem 1.2rem; text-align: left; font-weight: 700; color: var(--text-dark);">Nama Lengkap</th>
                                <th style="padding: 1.4rem 1.2rem; text-align: left; font-weight: 700; color: var(--text-dark);">Jurusan SMA</th>
                                <th style="padding: 1.4rem 1.2rem; text-align: left; font-weight: 700; color: var(--text-dark);">Email</th>
                                <th style="padding: 1.4rem 1.2rem; text-align: center; width: 280px; font-weight: 700; color: var(--text-dark);">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftar_siswa as $index => $siswa): ?>
                                <tr style="border-bottom: 1px solid var(--border); <?= $index % 2 === 0 ? 'background: var(--bg-card);' : 'background: var(--bg-light);' ?> transition: all 0.2s;">
                                    <td style="padding: 1.25rem 1.2rem; font-family: 'SF Mono', monospace; color: var(--primary); font-weight: 700;">
                                        <?= htmlspecialchars($siswa['nis']) ?>
                                    </td>
                                    <td style="padding: 1.25rem 1.2rem; font-weight: 700; color: var(--text-dark);">
                                        <?= htmlspecialchars($siswa['nama']) ?>
                                    </td>
                                    <td style="padding: 1.25rem 1.2rem;">
                                        <span class="badge" style="padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; background: <?= $siswa['jurusan_sma'] === 'IPA' ? 'var(--primary-light)' : 'var(--warning-light)' ?>; color: <?= $siswa['jurusan_sma'] === 'IPA' ? 'var(--primary)' : 'var(--warning)' ?>;">
                                            <?= htmlspecialchars($siswa['jurusan_sma']) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1.25rem 1.2rem; color: var(--text-muted);">
                                        <?= htmlspecialchars($siswa['email']) ?>
                                    </td>
                                    <td style="padding: 1.25rem 1.2rem; text-align: center;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                            <a href="../../laporan/index.php?search=<?= urlencode($siswa['nis']) ?>" 
                                               class="btn btn-outline btn-sm" style="background-color: var(--bg-light); color: var(--text-dark); border: 1px solid var(--border);">
                                               <i class="fa-solid fa-chart-simple"></i> Hasil
                                            </a>
                                            <a href="edit.php?id=<?= $siswa['id'] ?>" 
                                               class="btn btn-sm" style="background-color: var(--warning); color: white; font-weight: 700;">
                                               <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                            <a href="../../proses/proses_user.php?aksi=hapus&id=<?= $siswa['id'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                               <i class="fa-solid fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<style>
    tr:hover td {
        background-color: var(--primary-light) !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>