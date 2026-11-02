<?php
require_once 'config/database.php';

echo "<h2>Starting Database Migration</h2>";

// 1. Alter kriteria table to expand enum values for 'aspek'
$alter_query = "ALTER TABLE kriteria MODIFY COLUMN aspek ENUM('akademik','minat','bakat','kepribadian','ekonomi') NOT NULL";
if ($conn->query($alter_query)) {
    echo "✔ Successfully updated 'aspek' enum in kriteria table.<br>";
} else {
    die("❌ Failed to update 'aspek' enum: " . $conn->error);
}

// 2. Define the list of criteria (existing + new)
$criteria = [
    // Existing criteria to ensure they exist or get updated
    ['K01', 'Nilai Matematika',        'CF', 'akademik', 'Nilai Ujian/Rapor Matematika'],
    ['K02', 'Nilai Bahasa Indonesia',  'CF', 'akademik', 'Nilai Ujian/Rapor Bahasa Indonesia'],
    ['K03', 'Nilai Bahasa Inggris',    'CF', 'akademik', 'Nilai Ujian/Rapor Bahasa Inggris'],
    ['K04', 'Nilai IPA / Sains',       'CF', 'akademik', 'Nilai Ujian/Rapor Fisika/Kimia/Biologi/Sains'],
    ['K05', 'Nilai IPS',               'SF', 'akademik', 'Nilai Ujian/Rapor Sejarah/Sosiologi/Geografi'],
    ['K06', 'Nilai Ekonomi',           'SF', 'akademik', 'Nilai Ujian/Rapor Ekonomi/Akuntansi'],
    ['K07', 'Minat Teknologi',         'CF', 'minat', 'Ketertarikan pada pemrograman, IT, komputer, gadget'],
    ['K08', 'Minat Seni & Budaya',     'SF', 'minat', 'Ketertarikan pada desain, seni visual, musik, sastra'],
    ['K09', 'Minat Sosial & Humaniora','SF', 'minat', 'Ketertarikan pada interaksi sosial, politik, masyarakat'],
    ['K10', 'Kemampuan Analitis',      'CF', 'kepribadian', 'Kemampuan memecahkan masalah logis & terstruktur'],
    ['K11', 'Kemampuan Komunikasi',    'SF', 'kepribadian', 'Kemampuan menyampaikan gagasan secara verbal/tulisan'],
    ['K12', 'Kreativitas',             'SF', 'kepribadian', 'Kemampuan berpikir out-of-the-box dan inovatif'],
    
    // New criteria (Minat)
    ['K13', 'Minat Sains & Riset',     'CF', 'minat', 'Ketertarikan pada penelitian ilmiah, eksperimen, laboratorium'],
    ['K14', 'Minat Bisnis & Manajemen','SF', 'minat', 'Ketertarikan pada kewirausahaan, mengelola keuangan, organisasi'],
    ['K15', 'Minat Kesehatan & Medis', 'CF', 'minat', 'Ketertarikan pada medis, anatomi, menolong orang sakit'],
    ['K16', 'Minat Komunikasi & Bahasa','SF', 'minat', 'Ketertarikan pada sastra, pidato, jurnalistik, bahasa asing'],
    
    // New criteria (Bakat)
    ['K17', 'Bakat Numerik / Logika',  'CF', 'bakat', 'Kemampuan hitung cepat dan logika matematika'],
    ['K18', 'Bakat Verbal / Kebahasaan','CF', 'bakat', 'Kemampuan mengolah kata dan memahami bacaan'],
    ['K19', 'Bakat Spasial / Visual',  'SF', 'bakat', 'Kemampuan berimajinasi 3D, menggambar, tata letak'],
    ['K20', 'Bakat Mekanik / Teknis',  'SF', 'bakat', 'Kemampuan merakit, pemahaman alat/mesin, reaksi fisik/kimia'],
    ['K21', 'Bakat Klerikal / Ketelitian','SF', 'bakat', 'Ketelitian administratif, pencatatan data, keteraturan'],
    
    // New criteria (Kepribadian)
    ['K22', 'Kerja Sama Tim (Teamwork)','SF', 'kepribadian', 'Kemampuan berkolaborasi dan bekerja sama'],
    ['K23', 'Kepemimpinan (Leadership)','SF', 'kepribadian', 'Kemampuan memimpin dan mengambil keputusan'],
    
    // New criteria (Ekonomi)
    ['K24', 'Penghasilan Orang Tua',   'CF', 'ekonomi', 'Kapasitas finansial keluarga bulanan'],
    ['K25', 'Kesanggupan Biaya Kuliah','SF', 'ekonomi', 'Komitmen keuangan untuk pembiayaan kuliah mandiri']
];

echo "<h3>Inserting/updating criteria...</h3>";
$stmt = $conn->prepare("INSERT INTO kriteria (kode, nama_kriteria, tipe, aspek, keterangan) 
                       VALUES (?, ?, ?, ?, ?) 
                       ON DUPLICATE KEY UPDATE nama_kriteria = VALUES(nama_kriteria), tipe = VALUES(tipe), aspek = VALUES(aspek), keterangan = VALUES(keterangan)");

foreach ($criteria as $c) {
    $stmt->bind_param('sssss', $c[0], $c[1], $c[2], $c[3], $c[4]);
    if ($stmt->execute()) {
        echo "✔ Criteria {$c[0]} ({$c[1]}) processed.<br>";
    } else {
        echo "❌ Criteria {$c[0]} failed: " . $stmt->error . "<br>";
    }
}

// 3. Clear old ideal profiles to prevent partial/outdated profiles
echo "<h3>Clearing old ideal profiles...</h3>";
$conn->query("DELETE FROM profil_ideal");
echo "✔ Old ideal profiles cleared.<br>";

// 4. Seed ideal profiles for all 45 majors dynamically
echo "<h3>Seeding new ideal profiles for all 45 majors...</h3>";

$jurusan_query = $conn->query("SELECT id, kode, nama_jurusan FROM jurusan");
$kriteria_query = $conn->query("SELECT id, kode FROM kriteria");
$kriteria_ids = [];
while ($k = $kriteria_query->fetch_assoc()) {
    $kriteria_ids[$k['kode']] = $k['id'];
}

function getMajorCategory($name) {
    $name = strtolower($name);
    if (strpos($name, 'kedokteran') !== false || strpos($name, 'farmasi') !== false || strpos($name, 'keperawatan') !== false || strpos($name, 'gizi') !== false || strpos($name, 'kesehatan') !== false) {
        return 'Kesehatan';
    }
    if (strpos($name, 'informatika') !== false || strpos($name, 'sistem informasi') !== false || strpos($name, 'sipil') !== false || strpos($name, 'arsitektur') !== false || strpos($name, 'biologi') !== false || strpos($name, 'kimia') !== false || strpos($name, 'kelautan') !== false || strpos($name, 'mesin') !== false || strpos($name, 'statistika') !== false || strpos($name, 'pengairan') !== false || strpos($name, 'agronomi') !== false) {
        return 'Teknik';
    }
    if (strpos($name, 'hukum') !== false || strpos($name, 'komunikasi') !== false || strpos($name, 'hubungan internasional') !== false || strpos($name, 'sastra') !== false || strpos($name, 'perpustakaan') !== false) {
        return 'Sosial';
    }
    if (strpos($name, 'akuntansi') !== false || strpos($name, 'manajemen') !== false || strpos($name, 'administrasi bisnis') !== false || strpos($name, 'perbankan') !== false) {
        return 'Bisnis';
    }
    if (strpos($name, 'desain') !== false || strpos($name, 'seni') !== false) {
        return 'Seni';
    }
    if (strpos($name, 'pendidikan') !== false || strpos($name, 'guru') !== false) {
        return 'Pendidikan';
    }
    return 'Sosial'; // Fallback
}

// Default value templates for each category
$templates = [
    'Kesehatan' => [
        'K01' => 3.0, 'K02' => 3.0, 'K03' => 4.0, 'K04' => 5.0, 'K05' => 2.0, 'K06' => 2.0,
        'K07' => 3.0, 'K08' => 2.0, 'K09' => 4.0, 'K10' => 5.0, 'K11' => 4.0, 'K12' => 3.0,
        'K13' => 5.0, 'K14' => 2.0, 'K15' => 5.0, 'K16' => 3.0,
        'K17' => 4.0, 'K18' => 4.0, 'K19' => 3.0, 'K20' => 3.0, 'K21' => 5.0,
        'K22' => 4.0, 'K23' => 4.0,
        'K24' => 5.0, 'K25' => 5.0
    ],
    'Teknik' => [
        'K01' => 5.0, 'K02' => 3.0, 'K03' => 4.0, 'K04' => 5.0, 'K05' => 2.0, 'K06' => 2.0,
        'K07' => 5.0, 'K08' => 2.0, 'K09' => 2.0, 'K10' => 5.0, 'K11' => 3.0, 'K12' => 4.0,
        'K13' => 4.0, 'K14' => 3.0, 'K15' => 2.0, 'K16' => 3.0,
        'K17' => 5.0, 'K18' => 3.0, 'K19' => 4.0, 'K20' => 4.0, 'K21' => 4.0,
        'K22' => 4.0, 'K23' => 3.0,
        'K24' => 4.0, 'K25' => 4.0
    ],
    'Sosial' => [
        'K01' => 2.0, 'K02' => 5.0, 'K03' => 4.0, 'K04' => 2.0, 'K05' => 5.0, 'K06' => 3.0,
        'K07' => 3.0, 'K08' => 3.0, 'K09' => 5.0, 'K10' => 4.0, 'K11' => 5.0, 'K12' => 3.0,
        'K13' => 3.0, 'K14' => 3.0, 'K15' => 2.0, 'K16' => 5.0,
        'K17' => 2.0, 'K18' => 5.0, 'K19' => 3.0, 'K20' => 2.0, 'K21' => 4.0,
        'K22' => 4.0, 'K23' => 4.0,
        'K24' => 3.0, 'K25' => 3.0
    ],
    'Bisnis' => [
        'K01' => 4.0, 'K02' => 4.0, 'K03' => 4.0, 'K04' => 2.0, 'K05' => 4.0, 'K06' => 5.0,
        'K07' => 3.0, 'K08' => 2.0, 'K09' => 4.0, 'K10' => 4.0, 'K11' => 4.0, 'K12' => 3.0,
        'K13' => 3.0, 'K14' => 5.0, 'K15' => 2.0, 'K16' => 4.0,
        'K17' => 4.0, 'K18' => 4.0, 'K19' => 2.0, 'K20' => 2.0, 'K21' => 5.0,
        'K22' => 4.0, 'K23' => 4.0,
        'K24' => 3.0, 'K25' => 3.0
    ],
    'Seni' => [
        'K01' => 2.0, 'K02' => 4.0, 'K03' => 3.0, 'K04' => 2.0, 'K05' => 3.0, 'K06' => 2.0,
        'K07' => 4.0, 'K08' => 5.0, 'K09' => 3.0, 'K10' => 3.0, 'K11' => 3.0, 'K12' => 5.0,
        'K13' => 2.0, 'K14' => 3.0, 'K15' => 2.0, 'K16' => 4.0,
        'K17' => 2.0, 'K18' => 3.0, 'K19' => 5.0, 'K20' => 3.0, 'K21' => 3.0,
        'K22' => 4.0, 'K23' => 2.0,
        'K24' => 4.0, 'K25' => 4.0
    ],
    'Pendidikan' => [
        'K01' => 3.0, 'K02' => 4.0, 'K03' => 4.0, 'K04' => 3.0, 'K05' => 3.0, 'K06' => 3.0,
        'K07' => 3.0, 'K08' => 3.0, 'K09' => 5.0, 'K10' => 3.0, 'K11' => 5.0, 'K12' => 3.0,
        'K13' => 3.0, 'K14' => 2.0, 'K15' => 2.0, 'K16' => 5.0,
        'K17' => 3.0, 'K18' => 4.0, 'K19' => 3.0, 'K20' => 2.0, 'K21' => 4.0,
        'K22' => 5.0, 'K23' => 4.0,
        'K24' => 3.0, 'K25' => 3.0
    ]
];

$ins_stmt = $conn->prepare("INSERT INTO profil_ideal (jurusan_id, kriteria_id, nilai_ideal) VALUES (?, ?, ?)");

$total_seeded = 0;
while ($j = $jurusan_query->fetch_assoc()) {
    $category = getMajorCategory($j['nama_jurusan']);
    $template = $templates[$category];
    
    // Adjustments for specific majors to be extra precise
    if ($j['nama_jurusan'] == 'Arsitektur') {
        $template['K19'] = 5.0; // Higher spatial visual
    } elseif ($j['nama_jurusan'] == 'Statistika') {
        $template['K17'] = 5.0; // Higher numeric logic
        $template['K01'] = 5.0; // Math
    }
    
    foreach ($template as $kode => $nilai) {
        if (isset($kriteria_ids[$kode])) {
            $kid = $kriteria_ids[$kode];
            $ins_stmt->bind_param('iid', $j['id'], $kid, $nilai);
            $ins_stmt->execute();
            $total_seeded++;
        }
    }
}

echo "✔ Reseeded $total_seeded ideal profiles for 45 majors.<br>";
echo "<h3>Migration Completed Successfully!</h3>";
?>
