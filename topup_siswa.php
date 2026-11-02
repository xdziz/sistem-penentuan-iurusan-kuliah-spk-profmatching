<?php
/**
 * topup_siswa.php
 * Menambahkan siswa yang belum ada di database (additive, tidak menghapus data lama).
 * Juga meng-generate profil & rekomendasi untuk siswa yang belum punya.
 */

require_once 'config/database.php';

echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
      <title>Top-Up Data Siswa</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 20px; background:#f5f5f5; }
        h2   { color: #1D4ED8; }
        h3   { color: #374151; border-bottom: 2px solid #E5E7EB; padding-bottom:4px; }
        .ok  { color: #16A34A; }
        .err { color: #DC2626; }
        .warn{ color: #D97706; }
        .box { background:#fff; border-radius:8px; padding:16px 24px;
               box-shadow:0 2px 8px rgba(0,0,0,.08); margin-bottom:16px; }
        table{ border-collapse:collapse; width:100%; font-size:.9rem; }
        th,td{ border:1px solid #E5E7EB; padding:8px 12px; text-align:left; }
        th   { background:#F3F4F6; font-weight:700; }
      </style></head><body>";

echo "<div class='box'><h2>🔧 Top-Up Data Siswa</h2>
      <p>Script ini <strong>hanya menambahkan</strong> siswa yang belum ada.<br>
         Data yang sudah ada <strong>tidak akan dihapus atau diubah</strong>.</p></div>";

/* ══════════════════════════════════════════════════════════════
   LANGKAH 1 — Cek siswa mana yang sudah ada / belum
══════════════════════════════════════════════════════════════ */
echo "<div class='box'><h3>1. Diagnosa Database</h3>";

// Hitung total siswa sekarang
$total_now = $conn->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetch_row()[0];
$total_profil = $conn->query("SELECT COUNT(DISTINCT user_id) FROM siswa_profil")->fetch_row()[0];
$total_recom  = $conn->query("SELECT COUNT(DISTINCT user_id) FROM rekomendasi")->fetch_row()[0];

echo "<table>
  <tr><th>Keterangan</th><th>Jumlah</th></tr>
  <tr><td>Total siswa di tabel <code>users</code></td><td><strong>$total_now</strong></td></tr>
  <tr><td>Siswa yang punya data <code>siswa_profil</code></td><td><strong>$total_profil</strong></td></tr>
  <tr><td>Siswa yang punya data <code>rekomendasi</code></td><td><strong>$total_recom</strong></td></tr>
</table><br>";

// Cek NIS yang sudah ada
$existing_nis = [];
$r = $conn->query("SELECT nis FROM users WHERE role='siswa'");
while ($row = $r->fetch_row()) {
    $existing_nis[] = $row[0];
}
echo "<span class='ok'>NIS yang sudah ada (" . count($existing_nis) . "): " . implode(', ', $existing_nis) . "</span><br>";
echo "</div>";

/* ══════════════════════════════════════════════════════════════
   LANGKAH 2 — Daftar lengkap 32 siswa
══════════════════════════════════════════════════════════════ */
$semua_siswa = [
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

// Filter: hanya yang belum ada di database
$siswa_baru = array_filter($semua_siswa, fn($s) => !in_array($s['nis'], $existing_nis));
$siswa_baru = array_values($siswa_baru);

/* ══════════════════════════════════════════════════════════════
   LANGKAH 3 — Insert siswa yang belum ada
══════════════════════════════════════════════════════════════ */
echo "<div class='box'><h3>2. Tambah Siswa yang Belum Ada</h3>";

if (empty($siswa_baru)) {
    echo "<span class='warn'>Semua 32 siswa sudah ada di database. Tidak ada yang perlu ditambah.</span>";
    echo "</div>";
} else {
    echo "<span class='ok'>Ditemukan <strong>" . count($siswa_baru) . " siswa</strong> yang belum ada. Menambahkan...</span><br><br>";

    $password_hash = password_hash('siswa123', PASSWORD_BCRYPT);
    $stmt_user = $conn->prepare(
        "INSERT INTO users (nama, nis, email, password, role, kelas, jurusan_sma) VALUES (?, ?, ?, ?, 'siswa', ?, ?)"
    );
    $new_student_ids = [];

    foreach ($siswa_baru as $s) {
        $email_base = strtolower(str_replace([' ', "'"], ['.', ''], $s['nama']));
        $email      = $email_base . '@darulhikmah.sch.id';

        $stmt_user->bind_param('ssssss',
            $s['nama'], $s['nis'], $email,
            $password_hash, $s['kelas'], $s['jurusan_sma']
        );

        if ($stmt_user->execute()) {
            $sid = $stmt_user->insert_id;
            $new_student_ids[$sid] = $s;
            echo "<span class='ok'>✅ #{$s['nis']} – {$s['nama']} ({$s['kelas']}) berhasil ditambah.</span><br>";
        } else {
            echo "<span class='err'>❌ #{$s['nis']} – {$s['nama']} GAGAL: {$stmt_user->error}</span><br>";
        }
    }
    echo "<br><strong>Total ditambah: " . count($new_student_ids) . " siswa</strong>";
    echo "</div>";

    /* ══════════════════════════════════════════════════════════════
       LANGKAH 4 — Generate profil & rekomendasi siswa baru saja
    ══════════════════════════════════════════════════════════════ */
    echo "<div class='box'><h3>3. Generate Profil & Rekomendasi</h3>";

    // Ambil daftar kriteria aktif
    $kriteria_list = [];
    $r = $conn->query("SELECT id, kode, tipe, aspek FROM kriteria WHERE status = 1 ORDER BY id ASC");
    while ($row = $r->fetch_assoc()) {
        $kriteria_list[] = $row;
    }
    echo "<span class='ok'>Kriteria aktif ditemukan: <strong>" . count($kriteria_list) . " kriteria</strong></span><br>";

    // Ambil tabel bobot gap
    $tabel_bobot = [];
    $r = $conn->query("SELECT gap, bobot FROM bobot_gap");
    while ($row = $r->fetch_assoc()) {
        $tabel_bobot[(int)$row['gap']] = (float)$row['bobot'];
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
        "INSERT IGNORE INTO siswa_profil (user_id, kriteria_id, nilai) VALUES (?, ?, ?)"
    );
    $stmt_recom = $conn->prepare(
        "INSERT INTO rekomendasi (user_id, jurusan_id, nilai_cf, nilai_sf, nilai_total, ranking) VALUES (?, ?, ?, ?, ?, ?)"
    );

    function getBobotTopUp(int $gap, array $tabel): float {
        return $tabel[$gap] ?? 1.0;
    }

    $total_recom_baru = 0;

    foreach ($new_student_ids as $sid => $s) {
        $profile_values = [];

        foreach ($kriteria_list as $k) {
            $code = $k['kode'];
            $val  = 3.0;

            if ($s['jurusan_sma'] === 'IPA') {
                if (in_array($code, ['K01', 'K04', 'K13', 'K17', 'K20'])) {
                    $val = (float)rand(4, 5);
                } elseif (in_array($code, ['K05', 'K06', 'K14'])) {
                    $val = (float)rand(2, 3);
                } else {
                    $val = (float)rand(3, 4);
                }
            } else { // IPS
                if (in_array($code, ['K05', 'K06', 'K09', 'K11', 'K14', 'K21'])) {
                    $val = (float)rand(4, 5);
                } elseif (in_array($code, ['K01', 'K04', 'K13', 'K20'])) {
                    $val = (float)rand(2, 3);
                } else {
                    $val = (float)rand(3, 4);
                }
            }

            if (in_array($code, ['K24', 'K25'])) {
                $val = (float)rand(2, 5);
            }

            $profile_values[$k['id']] = $val;

            $stmt_profil->bind_param('iid', $sid, $k['id'], $val);
            $stmt_profil->execute();
        }

        // Hitung Profile Matching
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
                $bobot = getBobotTopUp($gap, $tabel_bobot);

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

        usort($results, fn($a, $b) => $b['nilai_total'] <=> $a['nilai_total']);

        // Hapus rekomendasi lama siswa ini (jika ada) lalu insert baru
        $del = $conn->prepare("DELETE FROM rekomendasi WHERE user_id = ?");
        $del->bind_param('i', $sid);
        $del->execute();

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
            $total_recom_baru++;
        }

        echo "<span class='ok'>✅ Profil & rekomendasi <strong>{$s['nama']}</strong> selesai.</span><br>";
    }

    echo "<br><strong>Total baris rekomendasi baru: $total_recom_baru</strong></div>";
}

/* ══════════════════════════════════════════════════════════════
   LANGKAH 5 — Cek siswa yang sudah ada tapi belum punya profil/rekomendasi
══════════════════════════════════════════════════════════════ */
echo "<div class='box'><h3>4. Perbaiki Siswa Lama yang Belum Punya Profil/Rekomendasi</h3>";

// Cari siswa yang ada di users tapi tidak punya rekomendasi sama sekali
$r = $conn->query("
    SELECT u.id, u.nama, u.nis, u.kelas, u.jurusan_sma
    FROM users u
    WHERE u.role = 'siswa'
      AND u.id NOT IN (SELECT DISTINCT user_id FROM rekomendasi)
    ORDER BY u.nis ASC
");
$siswa_tanpa_rekom = $r->fetch_all(MYSQLI_ASSOC);

if (empty($siswa_tanpa_rekom)) {
    echo "<span class='ok'>✅ Semua siswa sudah memiliki data rekomendasi.</span>";
} else {
    echo "<span class='warn'>⚠️ Ditemukan <strong>" . count($siswa_tanpa_rekom) . " siswa</strong> yang belum punya rekomendasi. Memproses...</span><br><br>";

    // Ambil data yang diperlukan (jika belum di-fetch sebelumnya)
    if (empty($kriteria_list)) {
        $kriteria_list = [];
        $r2 = $conn->query("SELECT id, kode, tipe, aspek FROM kriteria WHERE status = 1 ORDER BY id ASC");
        while ($row = $r2->fetch_assoc()) $kriteria_list[] = $row;

        $tabel_bobot = [];
        $r2 = $conn->query("SELECT gap, bobot FROM bobot_gap");
        while ($row = $r2->fetch_assoc()) $tabel_bobot[(int)$row['gap']] = (float)$row['bobot'];

        $jurusan_list = [];
        $r2 = $conn->query("SELECT id, nama_jurusan FROM jurusan WHERE status = 1");
        while ($row = $r2->fetch_assoc()) $jurusan_list[] = $row;

        $ideal_profiles = [];
        $r2 = $conn->query("SELECT jurusan_id, kriteria_id, nilai_ideal FROM profil_ideal");
        while ($row = $r2->fetch_assoc()) $ideal_profiles[$row['jurusan_id']][$row['kriteria_id']] = (float)$row['nilai_ideal'];

        $r2 = $conn->query("SELECT persen_cf, persen_sf FROM pengaturan_bobot LIMIT 1");
        $bobot_setting = $r2->fetch_assoc();
        $persen_cf = $bobot_setting['persen_cf'] / 100;
        $persen_sf = $bobot_setting['persen_sf'] / 100;

        $stmt_profil = $conn->prepare("INSERT IGNORE INTO siswa_profil (user_id, kriteria_id, nilai) VALUES (?, ?, ?)");
        $stmt_recom  = $conn->prepare("INSERT INTO rekomendasi (user_id, jurusan_id, nilai_cf, nilai_sf, nilai_total, ranking) VALUES (?, ?, ?, ?, ?, ?)");
    }

    $total_fixed = 0;
    foreach ($siswa_tanpa_rekom as $s) {
        $sid = $s['id'];
        $profile_values = [];

        // Cek apakah sudah punya profil
        $has_profil = $conn->query("SELECT COUNT(*) FROM siswa_profil WHERE user_id = $sid")->fetch_row()[0];

        if ($has_profil == 0) {
            foreach ($kriteria_list as $k) {
                $code = $k['kode'];
                $val  = 3.0;
                if ($s['jurusan_sma'] === 'IPA') {
                    if (in_array($code, ['K01','K04','K13','K17','K20'])) $val = (float)rand(4,5);
                    elseif (in_array($code, ['K05','K06','K14']))          $val = (float)rand(2,3);
                    else                                                    $val = (float)rand(3,4);
                } else {
                    if (in_array($code, ['K05','K06','K09','K11','K14','K21'])) $val = (float)rand(4,5);
                    elseif (in_array($code, ['K01','K04','K13','K20']))          $val = (float)rand(2,3);
                    else                                                          $val = (float)rand(3,4);
                }
                if (in_array($code, ['K24','K25'])) $val = (float)rand(2,5);
                $profile_values[$k['id']] = $val;
                $stmt_profil->bind_param('iid', $sid, $k['id'], $val);
                $stmt_profil->execute();
            }
        } else {
            // Sudah punya profil, ambil nilainya
            $r2 = $conn->query("SELECT kriteria_id, nilai FROM siswa_profil WHERE user_id = $sid");
            while ($row = $r2->fetch_assoc()) {
                $profile_values[$row['kriteria_id']] = (float)$row['nilai'];
            }
        }

        // Hitung & simpan rekomendasi
        $results = [];
        foreach ($jurusan_list as $jurusan) {
            $jid = $jurusan['id'];
            if (!isset($ideal_profiles[$jid])) continue;
            $cf_list = []; $sf_list = [];
            foreach ($kriteria_list as $k) {
                $kid = $k['id'];
                if (!isset($ideal_profiles[$jid][$kid]) || !isset($profile_values[$kid])) continue;
                $gap   = (int)($profile_values[$kid] - $ideal_profiles[$jid][$kid]);
                $bobot = getBobotTopUp($gap, $tabel_bobot);
                if ($k['tipe'] === 'CF') $cf_list[] = $bobot;
                else                     $sf_list[] = $bobot;
            }
            $ncf = !empty($cf_list) ? array_sum($cf_list)/count($cf_list) : 0;
            $nsf = !empty($sf_list) ? array_sum($sf_list)/count($sf_list) : 0;
            $results[] = [
                'jurusan_id'  => $jid,
                'nilai_cf'    => round($ncf, 4),
                'nilai_sf'    => round($nsf, 4),
                'nilai_total' => round(($persen_cf*$ncf)+($persen_sf*$nsf), 4),
            ];
        }
        usort($results, fn($a,$b) => $b['nilai_total'] <=> $a['nilai_total']);

        $del = $conn->prepare("DELETE FROM rekomendasi WHERE user_id = ?");
        $del->bind_param('i', $sid);
        $del->execute();

        foreach ($results as $rank => $res) {
            $ranking = $rank + 1;
            $stmt_recom->bind_param('iidddi', $sid, $res['jurusan_id'], $res['nilai_cf'], $res['nilai_sf'], $res['nilai_total'], $ranking);
            $stmt_recom->execute();
        }

        echo "<span class='ok'>✅ {$s['nama']} ({$s['nis']}) — profil & rekomendasi berhasil diperbaiki.</span><br>";
        $total_fixed++;
    }
    echo "<br><strong>Total diperbaiki: $total_fixed siswa</strong>";
}
echo "</div>";

/* ══════════════════════════════════════════════════════════════
   RINGKASAN AKHIR
══════════════════════════════════════════════════════════════ */
$final_siswa  = $conn->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetch_row()[0];
$final_profil = $conn->query("SELECT COUNT(DISTINCT user_id) FROM siswa_profil")->fetch_row()[0];
$final_recom  = $conn->query("SELECT COUNT(DISTINCT user_id) FROM rekomendasi")->fetch_row()[0];

echo "<div class='box' style='background:#ECFDF5;border-left:4px solid #16A34A;'>
      <h3 style='color:#15803D;margin:0'>✅ Selesai!</h3>
      <p style='margin:12px 0 0'>
        <strong>Kondisi database sekarang:</strong><br>
        • Total siswa di <code>users</code>: <strong>$final_siswa siswa</strong><br>
        • Siswa yang punya profil: <strong>$final_profil siswa</strong><br>
        • Siswa yang punya rekomendasi: <strong>$final_recom siswa</strong>
      </p>
      </div></body></html>";
?>
