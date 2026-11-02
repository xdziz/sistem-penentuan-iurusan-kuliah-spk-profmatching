<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = (int) $_SESSION['user_id'];

// ── 1. Ambil pengaturan bobot CF/SF ──────────────────────────────────────────
$q = $conn->query("SELECT persen_cf, persen_sf FROM pengaturan_bobot LIMIT 1");
$bobot_setting = $q->fetch_assoc();
$persen_cf = $bobot_setting['persen_cf'] / 100;
$persen_sf = $bobot_setting['persen_sf'] / 100;

// ── 2. Ambil semua jurusan aktif ──────────────────────────────────────────────
$jurusan_list = [];
$q = $conn->query("SELECT id, nama_jurusan FROM jurusan WHERE status = 1");
while ($row = $q->fetch_assoc()) {
    $jurusan_list[] = $row;
}

// ── 3. Ambil profil siswa ─────────────────────────────────────────────────────
$profil_siswa = [];
$q = $conn->prepare("
    SELECT sp.kriteria_id, sp.nilai, k.tipe
    FROM siswa_profil sp
    JOIN kriteria k ON k.id = sp.kriteria_id
    WHERE sp.user_id = ?
");
$q->bind_param('i', $user_id);
$q->execute();
$res = $q->get_result();
while ($row = $res->fetch_assoc()) {
    $profil_siswa[$row['kriteria_id']] = [
        'nilai' => (float) $row['nilai'],
        'tipe'  => $row['tipe'],
    ];
}

// ── 4. Ambil tabel bobot gap ──────────────────────────────────────────────────
$tabel_bobot = [];
$q = $conn->query("SELECT gap, bobot FROM bobot_gap");
while ($row = $q->fetch_assoc()) {
    $tabel_bobot[(int)$row['gap']] = (float) $row['bobot'];
}

function getBobot(int $gap, array $tabel): float {
    return isset($tabel[$gap]) ? $tabel[$gap] : 1.0;
}

// ── 5. Hitung Profile Matching per jurusan ────────────────────────────────────
$hasil = [];

foreach ($jurusan_list as $jurusan) {
    $jid = $jurusan['id'];

    $profil_ideal = [];
    $qi = $conn->prepare("
        SELECT pi.kriteria_id, pi.nilai_ideal, k.tipe
        FROM profil_ideal pi
        JOIN kriteria k ON k.id = pi.kriteria_id
        WHERE pi.jurusan_id = ?
    ");
    $qi->bind_param('i', $jid);
    $qi->execute();
    $ri = $qi->get_result();
    while ($row = $ri->fetch_assoc()) {
        $profil_ideal[$row['kriteria_id']] = [
            'nilai_ideal' => (float) $row['nilai_ideal'],
            'tipe'        => $row['tipe'],
        ];
    }

    if (empty($profil_ideal)) {
        continue;
    }

    $bobot_cf_list = [];
    $bobot_sf_list = [];

    foreach ($profil_ideal as $kid => $ideal) {
        $nilai_siswa = isset($profil_siswa[$kid]) ? $profil_siswa[$kid]['nilai'] : 0;
        $gap = (int)($nilai_siswa - $ideal['nilai_ideal']); // Rumus PM: Profil Siswa - Profil Ideal
        $bobot = getBobot($gap, $tabel_bobot);

        if ($ideal['tipe'] === 'CF') {
            $bobot_cf_list[] = $bobot;
        } else {
            $bobot_sf_list[] = $bobot;
        }
    }

    $ncf = !empty($bobot_cf_list) ? array_sum($bobot_cf_list) / count($bobot_cf_list) : 0;
    $nsf = !empty($bobot_sf_list) ? array_sum($bobot_sf_list) / count($bobot_sf_list) : 0;
    $nilai_total = ($persen_cf * $ncf) + ($persen_sf * $nsf);

    $hasil[] = [
        'jurusan_id'   => $jid,
        'nama_jurusan' => $jurusan['nama_jurusan'],
        'nilai_cf'     => round($ncf, 4),
        'nilai_sf'     => round($nsf, 4),
        'nilai_total'  => round($nilai_total, 4),
    ];
}

if (!empty($hasil)) {
    // Hapus hasil lama
    $del = $conn->prepare("DELETE FROM rekomendasi WHERE user_id = ?");
    $del->bind_param('i', $user_id);
    $del->execute();
    
    usort($hasil, fn($a, $b) => $b['nilai_total'] <=> $a['nilai_total']);

    $ins = $conn->prepare("
        INSERT INTO rekomendasi (user_id, jurusan_id, nilai_cf, nilai_sf, nilai_total, ranking)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($hasil as $rank => $item) {
        $ranking = $rank + 1;
        $ins->bind_param('iidddi', $user_id, $item['jurusan_id'], $item['nilai_cf'], $item['nilai_sf'], $item['nilai_total'], $ranking);
        $ins->execute();
    }
    
    header('Location: ../user/hasil.php?status=success_hitung');
} else {
    header('Location: ../user/isi_data.php?status=error_hitung');
}
exit;
