<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Parameter kelas & format
$kelas_filter = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
$format = isset($_GET['format']) ? strtolower(trim($_GET['format'])) : 'excel';

// Query data siswa
$where_clauses = ["u.role = 'siswa'"];
$params = [];
$types = '';

if ($kelas_filter !== '' && strtolower($kelas_filter) !== 'semua') {
    $where_clauses[] = "u.kelas = ?";
    $params[] = $kelas_filter;
    $types .= 's';
}

$where_sql = implode(' AND ', $where_clauses);

$query = "
    SELECT 
        u.id,
        u.nis,
        u.nama,
        u.kelas,
        u.jurusan_sma,
        u.email,
        r.nilai_cf,
        r.nilai_sf,
        r.nilai_total,
        r.ranking,
        r.tgl_hitung,
        j.nama_jurusan,
        j.universitas,
        j.fakultas
    FROM users u
    LEFT JOIN rekomendasi r ON r.user_id = u.id AND r.ranking = 1
    LEFT JOIN jurusan j ON j.id = r.jurusan_id
    WHERE $where_sql
    ORDER BY u.kelas ASC, u.nama ASC
";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query($query);
}

$data_siswa = [];
$count_sudah = 0;
$count_belum = 0;

while ($row = $res->fetch_assoc()) {
    $data_siswa[] = $row;
    if (!empty($row['nilai_total'])) {
        $count_sudah++;
    } else {
        $count_belum++;
    }
}

$total_siswa = count($data_siswa);
$label_kelas = (!empty($kelas_filter) && strtolower($kelas_filter) !== 'semua') ? $kelas_filter : 'Semua Kelas';
$safe_filename_kelas = preg_replace('/[^a-zA-Z0-9_-]/', '_', $label_kelas);
$timestamp = date('Ymd_His');

if ($format === 'csv') {
    // Export format CSV dengan UTF-8 BOM
    $filename = "Data_Siswa_{$safe_filename_kelas}_{$timestamp}.csv";
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // UTF-8 BOM agar Excel membuka teks dan simbol UTF-8 dengan benar
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Header CSV
    fputcsv($output, [
        'No',
        'NIS',
        'Nama Lengkap Siswa',
        'Kelas',
        'Jurusan SMA',
        'Email',
        'Rekomendasi Jurusan (SPK)',
        'Universitas',
        'Nilai CF',
        'Nilai SF',
        'Nilai Total SPK',
        'Status SPK',
        'Tanggal Hitung'
    ]);

    $no = 1;
    foreach ($data_siswa as $s) {
        $status = !empty($s['nilai_total']) ? 'Sudah Dihitung' : 'Belum Dihitung';
        fputcsv($output, [
            $no++,
            $s['nis'],
            $s['nama'],
            $s['kelas'] ?: '-',
            $s['jurusan_sma'] ?: '-',
            $s['email'],
            $s['nama_jurusan'] ?: '-',
            $s['universitas'] ?: '-',
            !empty($s['nilai_cf']) ? number_format((float)$s['nilai_cf'], 4, '.', '') : '-',
            !empty($s['nilai_sf']) ? number_format((float)$s['nilai_sf'], 4, '.', '') : '-',
            !empty($s['nilai_total']) ? number_format((float)$s['nilai_total'], 4, '.', '') : '-',
            $status,
            !empty($s['tgl_hitung']) ? date('d/m/Y H:i', strtotime($s['tgl_hitung'])) : '-'
        ]);
    }
    fclose($output);
    exit;
}

// Default: Export Excel HTML Spreadsheet (.xls)
$filename = "Data_Siswa_{$safe_filename_kelas}_{$timestamp}.xls";
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name><?= htmlspecialchars(substr($label_kelas, 0, 31)) ?></x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
     <x:Print>
      <x:ValidPrinterInfo/>
      <x:PaperSizeIndex>9</x:PaperSizeIndex>
      <x:Scale>85</x:Scale>
      <x:HorizontalResolution>600</x:HorizontalResolution>
      <x:VerticalResolution>600</x:VerticalResolution>
     </x:Print>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
  body {
    font-family: 'Calibri', 'Arial', sans-serif;
    color: #1E293B;
  }
  .title-main {
    font-size: 16pt;
    font-weight: bold;
    color: #1E3A8A;
    text-align: center;
  }
  .title-sub {
    font-size: 12pt;
    font-weight: bold;
    color: #334155;
    text-align: center;
  }
  .meta-label {
    font-weight: bold;
    color: #475569;
  }
  table.data-table {
    border-collapse: collapse;
    width: 100%;
  }
  table.data-table th {
    background-color: #2563EB;
    color: #FFFFFF;
    font-weight: bold;
    border: 1px solid #1D4ED8;
    padding: 10px 8px;
    text-align: center;
    vertical-align: middle;
    font-size: 10pt;
  }
  table.data-table td {
    border: 1px solid #CBD5E1;
    padding: 8px;
    font-size: 9.5pt;
    vertical-align: middle;
  }
  .text-center {
    text-align: center;
  }
  .text-left {
    text-align: left;
  }
  .text-right {
    text-align: right;
  }
  .nis-cell {
    mso-number-format: "\@";
    text-align: center;
  }
  .score-cell {
    mso-number-format: "0\.0000";
    text-align: center;
  }
  .badge-success {
    background-color: #DCFCE7;
    color: #166534;
    font-weight: bold;
    text-align: center;
  }
  .badge-warning {
    background-color: #FEF3C7;
    color: #92400E;
    text-align: center;
  }
  .summary-box {
    background-color: #F1F5F9;
    font-weight: bold;
    border: 1px solid #CBD5E1;
    font-size: 9.5pt;
  }
</style>
</head>
<body>

<table>
  <tr>
    <td colspan="12" class="title-main">MA DAARUL HIKMAH PAMULANG</td>
  </tr>
  <tr>
    <td colspan="12" class="title-sub">LAPORAN DATA SISWA & HASIL REKOMENDASI JURUSAN PERGURUAN TINGGI</td>
  </tr>
  <tr>
    <td colspan="12" style="text-align: center; color: #64748B; font-size: 10pt;">Metode Profile Matching</td>
  </tr>
  <tr><td colspan="12"></td></tr>
  <tr>
    <td colspan="2" class="meta-label">Kelas:</td>
    <td colspan="4" style="font-weight: bold;"><?= htmlspecialchars($label_kelas) ?></td>
    <td colspan="2" class="meta-label">Tanggal Cetak:</td>
    <td colspan="4"><?= date('d F Y, H:i') ?> WIB</td>
  </tr>
  <tr>
    <td colspan="2" class="meta-label">Total Siswa:</td>
    <td colspan="4"><?= $total_siswa ?> Siswa</td>
    <td colspan="2" class="meta-label">Status Perhitungan:</td>
    <td colspan="4"><?= $count_sudah ?> Sudah Dihitung, <?= $count_belum ?> Belum Dihitung</td>
  </tr>
  <tr><td colspan="12"></td></tr>
</table>

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 45px;">No</th>
      <th style="width: 120px;">NIS</th>
      <th style="width: 220px;">Nama Lengkap</th>
      <th style="width: 90px;">Kelas</th>
      <th style="width: 100px;">Jurusan SMA</th>
      <th style="width: 220px;">Email</th>
      <th style="width: 200px;">Rekomendasi Jurusan (SPK)</th>
      <th style="width: 200px;">Universitas</th>
      <th style="width: 90px;">Core Factor (CF)</th>
      <th style="width: 90px;">Secondary Factor (SF)</th>
      <th style="width: 100px;">Skor Akhir SPK</th>
      <th style="width: 130px;">Status SPK</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($data_siswa)): ?>
      <tr>
        <td colspan="12" class="text-center" style="padding: 20px; color: #64748B;">Tidak ada data siswa untuk kelas ini.</td>
      </tr>
    <?php else: ?>
      <?php 
      $no = 1; 
      foreach ($data_siswa as $index => $row): 
        $bg_color = ($index % 2 === 0) ? '#FFFFFF' : '#F8FAFC';
        $has_score = !empty($row['nilai_total']);
      ?>
        <tr style="background-color: <?= $bg_color ?>;">
          <td class="text-center"><?= $no++ ?></td>
          <td class="nis-cell"><?= htmlspecialchars($row['nis']) ?></td>
          <td class="text-left" style="font-weight: 600;"><?= htmlspecialchars($row['nama']) ?></td>
          <td class="text-center"><?= htmlspecialchars($row['kelas'] ?: '-') ?></td>
          <td class="text-center"><?= htmlspecialchars($row['jurusan_sma'] ?: '-') ?></td>
          <td class="text-left"><?= htmlspecialchars($row['email']) ?></td>
          <td class="text-left"><?= htmlspecialchars($row['nama_jurusan'] ?: '-') ?></td>
          <td class="text-left"><?= htmlspecialchars($row['universitas'] ?: '-') ?></td>
          <td class="score-cell"><?= $has_score ? number_format((float)$row['nilai_cf'], 4, '.', '') : '-' ?></td>
          <td class="score-cell"><?= $has_score ? number_format((float)$row['nilai_sf'], 4, '.', '') : '-' ?></td>
          <td class="score-cell" style="font-weight: bold; color: #2563EB;"><?= $has_score ? number_format((float)$row['nilai_total'], 4, '.', '') : '-' ?></td>
          <td class="<?= $has_score ? 'badge-success' : 'badge-warning' ?>">
            <?= $has_score ? 'Sudah Dihitung' : 'Belum Dihitung' ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="12" style="border: none; padding-top: 15px;"></td>
    </tr>
    <tr>
      <td colspan="3" class="summary-box">Total Siswa Terdaftar: <?= $total_siswa ?></td>
      <td colspan="4" class="summary-box">Siswa Selesai Dihitung: <?= $count_sudah ?></td>
      <td colspan="5" class="summary-box">Siswa Belum Dihitung: <?= $count_belum ?></td>
    </tr>
  </tfoot>
</table>

<br><br>
<table style="width: 100%; border: none;">
  <tr>
    <td colspan="4" style="text-align: center; border: none;">
      Mengetahui,<br>
      <strong>Kepala Sekolah</strong><br><br><br><br>
      ( __________________________ )
    </td>
    <td colspan="4" style="border: none;"></td>
    <td colspan="4" style="text-align: center; border: none;">
      Pamulang, <?= date('d F Y') ?><br>
      <strong>Administrator SPK</strong><br><br><br><br>
      ( <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator') ?> )
    </td>
  </tr>
</table>

</body>
</html>
