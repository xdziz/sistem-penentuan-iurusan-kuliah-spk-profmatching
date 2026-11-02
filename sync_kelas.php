<?php
/**
 * sync_kelas.php
 * Tool untuk sinkronisasi dan pembagian data siswa ke kelas XII IPA & XII IPS
 */

require_once 'config/database.php';

// Pastikan kelas XII IPA dan XII IPS terdaftar di database
$conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPA')");
$conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPS')");

$action = $_GET['action'] ?? '';
$msg = '';

if ($action === 'bagi_rata') {
    $siswa_query = $conn->query("SELECT id FROM users WHERE role = 'siswa' ORDER BY nis ASC, id ASC");
    $siswa_ids = [];
    while ($r = $siswa_query->fetch_assoc()) {
        $siswa_ids[] = (int)$r['id'];
    }
    $total = count($siswa_ids);
    if ($total > 0) {
        $half = (int)ceil($total / 2);
        $ipa_ids = array_slice($siswa_ids, 0, $half);
        $ips_ids = array_slice($siswa_ids, $half);

        if (!empty($ipa_ids)) {
            $conn->query("UPDATE users SET kelas = 'XII IPA', jurusan_sma = 'IPA' WHERE id IN (" . implode(',', $ipa_ids) . ")");
        }
        if (!empty($ips_ids)) {
            $conn->query("UPDATE users SET kelas = 'XII IPS', jurusan_sma = 'IPS' WHERE id IN (" . implode(',', $ips_ids) . ")");
        }
        $msg = "Berhasil membagi rata $total siswa: " . count($ipa_ids) . " Siswa ke XII IPA dan " . count($ips_ids) . " Siswa ke XII IPS.";
    }
} elseif ($action === 'sinkron_jurusan') {
    $ips_res = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND UPPER(jurusan_sma) = 'IPS'");
    $ips_count = $ips_res ? (int)$ips_res->fetch_row()[0] : 0;

    if ($ips_count > 0) {
        $conn->query("UPDATE users SET kelas = 'XII IPS' WHERE role = 'siswa' AND UPPER(jurusan_sma) = 'IPS'");
        $conn->query("UPDATE users SET kelas = 'XII IPA' WHERE role = 'siswa' AND (UPPER(jurusan_sma) = 'IPA' OR jurusan_sma IS NULL OR jurusan_sma = '')");
        $msg = "Berhasil menyinkronkan kelas sesuai jurusan_sma masing-masing siswa.";
    } else {
        // Fallback jika belum ada jurusan IPS
        header("Location: sync_kelas.php?action=bagi_rata");
        exit;
    }
}

// Statistik terbaru
$total_siswa = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetch_row()[0];
$siswa_ipa = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND kelas = 'XII IPA'")->fetch_row()[0];
$siswa_ips = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND kelas = 'XII IPS'")->fetch_row()[0];
$siswa_tanpa_kelas = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND (kelas IS NULL OR kelas NOT IN ('XII IPA', 'XII IPS'))")->fetch_row()[0];

// Daftar siswa
$siswa_list = $conn->query("SELECT id, nis, nama, kelas, jurusan_sma FROM users WHERE role = 'siswa' ORDER BY kelas ASC, nis ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinkronisasi Data Siswa Kelas - MA Daarul Hikmah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: #1e293b;
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            border: 1px solid #334155;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #4f46e5;
            color: white;
        }
        .btn-primary:hover {
            background: #4338ca;
        }
        .btn-warning {
            background: #f59e0b;
            color: #1e1e2d;
        }
        .btn-outline {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
        }
        .btn-outline:hover {
            color: white;
            border-color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-box {
            background: #0f172a;
            border: 1px solid #334155;
            padding: 1.25rem;
            border-radius: 12px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            margin-top: 0.25rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #334155;
        }
        th {
            background: #0f172a;
            color: #94a3b8;
        }
        .badge {
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .badge-ipa {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }
        .badge-ips {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="margin: 0 0 0.5rem 0; font-size: 1.8rem;">
                <i class="fa-solid fa-graduation-cap" style="color: #6366f1;"></i> Penyesuaian Data Siswa Kelas
            </h1>
            <p style="color: #94a3b8; margin: 0 0 1.5rem 0;">
                Kelola dan bagi rata penempatan siswa ke dalam kelas <strong>XII IPA</strong> dan <strong>XII IPS</strong> secara otomatis.
            </p>

            <?php if (!empty($msg)): ?>
                <div style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-box">
                    <div style="color: #94a3b8; font-size: 0.85rem;">Total Siswa</div>
                    <div class="stat-number" style="color: #f8fafc;"><?= $total_siswa ?></div>
                </div>
                <div class="stat-box">
                    <div style="color: #60a5fa; font-size: 0.85rem;">Kelas XII IPA</div>
                    <div class="stat-number" style="color: #60a5fa;"><?= $siswa_ipa ?></div>
                </div>
                <div class="stat-box">
                    <div style="color: #fbbf24; font-size: 0.85rem;">Kelas XII IPS</div>
                    <div class="stat-number" style="color: #fbbf24;"><?= $siswa_ips ?></div>
                </div>
                <?php if ($siswa_tanpa_kelas > 0): ?>
                    <div class="stat-box">
                        <div style="color: #f87171; font-size: 0.85rem;">Belum Ditentukan</div>
                        <div class="stat-number" style="color: #f87171;"><?= $siswa_tanpa_kelas ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="sync_kelas.php?action=bagi_rata" class="btn btn-primary" onclick="return confirm('Bagi rata siswa menjadi dua kelompok (50% IPA dan 50% IPS)?')">
                    <i class="fa-solid fa-arrows-split-up-and-left"></i> Bagi Rata Siswa (50:50)
                </a>
                <a href="sync_kelas.php?action=sinkron_jurusan" class="btn btn-warning" onclick="return confirm('Sinkronkan kelas sesuai jurusan SMA?')">
                    <i class="fa-solid fa-rotate"></i> Sinkronkan Sesuai Jurusan
                </a>
                <a href="admin/kelas/index.php" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Kelas
                </a>
                <a href="admin/siswa/index.php" class="btn btn-outline">
                    <i class="fa-solid fa-users"></i> Buka Data Siswa
                </a>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Daftar Siswa Saat Ini (<?= count($siswa_list) ?>)</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan SMA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa_list as $i => $s): 
                            $is_ipa = $s['kelas'] === 'XII IPA';
                        ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code><?= htmlspecialchars($s['nis']) ?></code></td>
                                <td><strong><?= htmlspecialchars($s['nama']) ?></strong></td>
                                <td>
                                    <span class="badge <?= $is_ipa ? 'badge-ipa' : 'badge-ips' ?>">
                                        <?= htmlspecialchars($s['kelas'] ?: 'Belum Ada') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $s['jurusan_sma'] === 'IPA' ? 'badge-ipa' : 'badge-ips' ?>">
                                        <?= htmlspecialchars($s['jurusan_sma'] ?: '-') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
