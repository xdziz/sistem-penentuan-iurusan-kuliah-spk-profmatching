<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

if ($aksi === 'tambah') {
    $nama_kelas = $_POST['nama_kelas'];
    $stmt = $conn->prepare("INSERT INTO kelas (nama_kelas) VALUES (?)");
    $stmt->bind_param('s', $nama_kelas);
    if ($stmt->execute()) {
        header('Location: index.php?status=success_tambah');
    } else {
        header('Location: index.php?status=error_tambah');
    }
}

elseif ($aksi === 'edit') {
    $id = (int)$_POST['id'];
    $nama_kelas = $_POST['nama_kelas'];
    $stmt = $conn->prepare("UPDATE kelas SET nama_kelas = ? WHERE id = ?");
    $stmt->bind_param('si', $nama_kelas, $id);
    if ($stmt->execute()) {
        header('Location: index.php?status=success_edit');
    } else {
        header('Location: index.php?status=error_edit');
    }
}

elseif ($aksi === 'hapus') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM kelas WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header('Location: index.php?status=success_hapus');
    } else {
        header('Location: index.php?status=error_hapus');
    }
}

elseif ($aksi === 'bagi_rata') {
    // Pastikan kelas XII IPA dan XII IPS tersedia
    $conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPA')");
    $conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPS')");

    // Ambil seluruh siswa
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
    }

    header('Location: index.php?status=success_bagi');
}

elseif ($aksi === 'sinkron_jurusan') {
    // Pastikan kelas XII IPA dan XII IPS tersedia
    $conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPA')");
    $conn->query("INSERT IGNORE INTO kelas (nama_kelas) VALUES ('XII IPS')");

    // Cek apakah ada data siswa dengan jurusan_sma IPS
    $ips_res = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND UPPER(jurusan_sma) = 'IPS'");
    $ips_by_major = $ips_res ? (int)$ips_res->fetch_row()[0] : 0;

    if ($ips_by_major > 0) {
        // Sinkronkan kelas dengan jurusan masing-masing
        $conn->query("UPDATE users SET kelas = 'XII IPS' WHERE role = 'siswa' AND UPPER(jurusan_sma) = 'IPS'");
        $conn->query("UPDATE users SET kelas = 'XII IPA' WHERE role = 'siswa' AND (UPPER(jurusan_sma) = 'IPA' OR jurusan_sma IS NULL OR jurusan_sma = '')");
    } else {
        // Fallback bagi rata jika semua belum ada jurusan IPS
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

    header('Location: index.php?status=success_sync');
}

exit;
