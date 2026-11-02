<?php
/**
 * seed_siswa.php
 * Seeding data siswa untuk Sistem Rekomendasi Jurusan
 *
 * Ketentuan:
 *  - Total siswa : 32
 *  - Kelas       : XII IPA (16 siswa) | XII IPS (16 siswa)
 *  - Jurusan SMA : IPA dan IPS saja (Bahasa dihapus)
 *  - Password    : siswa123
 */

require_once 'config/database.php';

echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
      <title>Seeding Siswa</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 20px; background:#f5f5f5; }
        h2   { color: #1D4ED8; }
        h3   { color: #374151; border-bottom: 2px solid #E5E7EB; padding-bottom:4px; }
        .ok  { color: #16A34A; }
        .err { color: #DC2626; }
        .box { background:#fff; border-radius:8px; padding:16px 24px;
               box-shadow:0 2px 8px rgba(0,0,0,.08); margin-bottom:16px; }
      </style></head><body>";

echo "<div class='box'><h2>Seeding Data Siswa</h2>
      <p>Total: <strong>32 siswa</strong> | XII IPA = 16 siswa | XII IPS = 16 siswa</p></div>";

/* ══════════════════════════════════════════════════════════════
   LANGKAH 1 — Reset & seed tabel kelas (IPA & IPS saja)
══════════════════════════════════════════════════════════════ */
echo "<div class='box'><h3>1. Reset Tabel Kelas</h3>";

$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE kelas");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$kelas_list = [
    ['nama' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'XII IPS', 'jurusan_sma' => 'IPS'],
];

$stmt_kelas = $conn->prepare("INSERT INTO kelas (nama_kelas) VALUES (?)");
foreach ($kelas_list as $k) {
    $stmt_kelas->bind_param('s', $k['nama']);
    $stmt_kelas->execute();
    echo "<span class='ok'>Kelas '{$k['nama']}' berhasil ditambahkan.</span><br>";
}
echo "</div>";

/* ══════════════════════════════════════════════════════════════
   LANGKAH 2 — Hapus semua siswa lama & buat 32 siswa baru
══════════════════════════════════════════════════════════════ */
echo "<div class='box'><h3>2. Generate 32 Siswa</h3>";

// Hapus semua data siswa lama beserta relasi
$conn->query("DELETE FROM rekomendasi WHERE user_id IN (SELECT id FROM users WHERE role='siswa')");
$conn->query("DELETE FROM siswa_profil WHERE user_id IN (SELECT id FROM users WHERE role='siswa')");
$conn->query("DELETE FROM users WHERE role = 'siswa'");
echo "<span class='ok'>Data siswa lama berhasil dihapus.</span><br><br>";

// 32 siswa dengan nama eksplisit (16 IPA + 16 IPS)
$siswa_data = [
    // XII IPA (16 siswa)
    ['nama' => 'Ahmad Pratama',     'nis' => '12260001', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Muhammad Hidayat',  'nis' => '12260002', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Rian Saputra',      'nis' => '12260003', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Fikri Kurniawan',   'nis' => '12260004', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Rizky Nugroho',     'nis' => '12260005', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Dimas Setiawan',    'nis' => '12260006', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Fajar Wijaya',      'nis' => '12260007', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Yusuf Fadillah',    'nis' => '12260008', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Fatimah Rahmawati', 'nis' => '12260009', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Aisyah Putri',      'nis' => '12260010', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Rahma Lestari',     'nis' => '12260011', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Indah Fitriani',    'nis' => '12260012', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Nadia Wulandari',   'nis' => '12260013', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Zahra Utami',       'nis' => '12260014', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Luthfi Ramadhan',   'nis' => '12260015', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],
    ['nama' => 'Bagus Kusuma',      'nis' => '12260016', 'kelas' => 'XII IPA', 'jurusan_sma' => 'IPA'],

    // XII IPS (16 siswa)
    ['nama' => 'Aditya Siregar',    'nis' => '12260017', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Taufik Nasution',   'nis' => '12260018', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Aldi Lubis',        'nis' => '12260019', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Bima Tanjung',      'nis' => '12260020', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Bayu Ginting',      'nis' => '12260021', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Eko Hasibuan',      'nis' => '12260022', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Budi Manurung',     'nis' => '12260023', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Agus Sinaga',       'nis' => '12260024', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Sari Wulandari',    'nis' => '12260025', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Kartika Sari',      'nis' => '12260026', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Mega Fitriani',     'nis' => '12260027', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Fitri Raharjo',     'nis' => '12260028', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Rani Anwar',        'nis' => '12260029', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Dian Hadi',         'nis' => '12260030', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Nabila Prastyo',    'nis' => '12260031', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
    ['nama' => 'Salma Ritonga',     'nis' => '12260032', 'kelas' => 'XII IPS', 'jurusan_sma' => 'IPS'],
];

$password_hash = password_hash('siswa123', PASSWORD_BCRYPT);
$stmt_user     = $conn->prepare(
    "INSERT INTO users (nama, nis, email, password, role, kelas, jurusan_sma) VALUES (?, ?, ?, ?, 'siswa', ?, ?)"
);
$student_ids = [];

foreach ($siswa_data as $s) {
    $email_base = strtolower(str_replace(' ', '.', $s['nama']));
    $email      = $email_base . '@darulhikmah.sch.id';

    $stmt_user->bind_param('ssssss',
        $s['nama'], $s['nis'], $email,
        $password_hash, $s['kelas'], $s['jurusan_sma']
    );

    if ($stmt_user->execute()) {
        $sid               = $stmt_user->insert_id;
        $student_ids[$sid] = $s;
        echo "<span class='ok'>#{$s['nis']} – {$s['nama']} ({$s['kelas']})</span><br>";
    } else {
        echo "<span class='err'>#{$s['nis']} – {$s['nama']} GAGAL: {$stmt_user->error}</span><br>";
    }
}

echo "<br><strong>Total berhasil: " . count($student_ids) . " siswa</strong></div>";

/* ══════════════════════════════════════════════════════════════
   LANGKAH 3 — Seed profil siswa & hitung rekomendasi
══════════════════════════════════════════════════════════════ */
echo "<div class='box'><h3>3. Seeding Profil &amp; Perhitungan Rekomendasi</h3>";

// Ambil daftar kriteria aktif
$kriteria_list = [];
$r = $conn->query("SELECT id, kode, tipe, aspek FROM kriteria WHERE status = 1");
while ($row = $r->fetch_assoc()) {
    $kriteria_list[] = $row;
}

// Ambil tabel bobot gap
$tabel_bobot = [];
$r = $conn->query("SELECT gap, bobot FROM bobot_gap");
while ($row = $r->fetch_assoc()) {
    $tabel_bobot[(int)$row['gap']] = (float)$row['bobot'];
}

function getBobotValue(int $gap, array $tabel): float {
    return $tabel[$gap] ?? 1.0;
}

// Ambil semua jurusan kuliah aktif
$jurusan_list = [];
$r = $conn->query("SELECT id, nama_jurusan FROM jurusan WHERE status = 1");
while ($row = $r->fetch_assoc()) {
    $jurusan_list[] = $row;
}

// Ambil semua profil ideal
$ideal_profiles = [];
$r = $conn->query("SELECT jurusan_id, kriteria_id, nilai_ideal FROM profil_ideal");
while ($row = $r->fetch_assoc()) {
    $ideal_profiles[$row['jurusan_id']][$row['kriteria_id']] = (float)$row['nilai_ideal'];
}

// Pengaturan bobot CF/SF
$r             = $conn->query("SELECT persen_cf, persen_sf FROM pengaturan_bobot LIMIT 1");
$bobot_setting = $r->fetch_assoc();
$persen_cf     = $bobot_setting['persen_cf'] / 100;
$persen_sf     = $bobot_setting['persen_sf'] / 100;

$stmt_profil = $conn->prepare(
    "INSERT INTO siswa_profil (user_id, kriteria_id, nilai) VALUES (?, ?, ?)"
);
$stmt_recom  = $conn->prepare(
    "INSERT INTO rekomendasi (user_id, jurusan_id, nilai_cf, nilai_sf, nilai_total, ranking) VALUES (?, ?, ?, ?, ?, ?)"
);

$total_recom = 0;

foreach ($student_ids as $sid => $s) {
    $profile_values = [];

    foreach ($kriteria_list as $k) {
        $code = $k['kode'];
        $val  = 3.0;

        if ($s['jurusan_sma'] === 'IPA') {
            // Siswa IPA: unggul sains & matematika
            if (in_array($code, ['K01', 'K04', 'K13', 'K17', 'K20'])) {
                $val = (float)rand(4, 5);   // tinggi sains/mat
            } elseif (in_array($code, ['K05', 'K06', 'K14'])) {
                $val = (float)rand(2, 3);   // lebih rendah sosial
            } else {
                $val = (float)rand(3, 4);
            }
        } else { // IPS
            // Siswa IPS: unggul sosial, ekonomi, komunikasi
            if (in_array($code, ['K05', 'K06', 'K09', 'K11', 'K14', 'K21'])) {
                $val = (float)rand(4, 5);   // tinggi sosial/bisnis
            } elseif (in_array($code, ['K01', 'K04', 'K13', 'K20'])) {
                $val = (float)rand(2, 3);   // lebih rendah sains
            } else {
                $val = (float)rand(3, 4);
            }
        }

        // Pendapatan & keterjangkauan biaya -> acak merata
        if (in_array($code, ['K24', 'K25'])) {
            $val = (float)rand(2, 5);
        }

        $profile_values[$k['id']] = $val;

        $stmt_profil->bind_param('iid', $sid, $k['id'], $val);
        $stmt_profil->execute();
    }

    // Hitung Profile Matching untuk setiap jurusan kuliah
    $results = [];

    foreach ($jurusan_list as $jurusan) {
        $jid = $jurusan['id'];
        if (!isset($ideal_profiles[$jid])) continue;

        $cf_list = [];
        $sf_list = [];

        foreach ($kriteria_list as $k) {
            $kid = $k['id'];
            if (!isset($ideal_profiles[$jid][$kid])) continue;

            $gap   = (int)($profile_values[$kid] - $ideal_profiles[$jid][$kid]);
            $bobot = getBobotValue($gap, $tabel_bobot);

            if ($k['tipe'] === 'CF') {
                $cf_list[] = $bobot;
            } else {
                $sf_list[] = $bobot;
            }
        }

        $ncf         = !empty($cf_list) ? array_sum($cf_list) / count($cf_list) : 0;
        $nsf         = !empty($sf_list) ? array_sum($sf_list) / count($sf_list) : 0;
        $nilai_total = ($persen_cf * $ncf) + ($persen_sf * $nsf);

        $results[] = [
            'jurusan_id'  => $jid,
            'nilai_cf'    => round($ncf, 4),
            'nilai_sf'    => round($nsf, 4),
            'nilai_total' => round($nilai_total, 4),
        ];
    }

    // Urutkan: nilai_total tertinggi = ranking 1
    usort($results, fn($a, $b) => $b['nilai_total'] <=> $a['nilai_total']);

    foreach ($results as $rank => $res) {
        $ranking = $rank + 1;
        $stmt_recom->bind_param(
            'iidddi',
            $sid,
            $res['jurusan_id'],
            $res['nilai_cf'],
            $res['nilai_sf'],
            $res['nilai_total'],
            $ranking
        );
        $stmt_recom->execute();
        $total_recom++;
    }

    echo "<span class='ok'>Profil &amp; rekomendasi {$s['nama']} selesai.</span><br>";
}

echo "<br><strong>" . count($student_ids) . " profil siswa tersimpan. "
   . "$total_recom baris rekomendasi dihitung.</strong></div>";

echo "<div class='box' style='background:#ECFDF5;border-left:4px solid #16A34A;'>
      <h3 style='color:#15803D;margin:0'>Seeding Selesai!</h3>
      <p style='margin:8px 0 0'>
        <strong>32 siswa</strong> berhasil ditambahkan:<br>
        &bull; XII IPA = 16 siswa<br>
        &bull; XII IPS = 16 siswa<br>
        &bull; Kelas XII Bahasa <strong>telah dihapus</strong><br>
        &bull; Password default semua siswa: <code>siswa123</code>
      </p>
      </div></body></html>";
?>
