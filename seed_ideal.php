<?php
require_once 'config/database.php';

echo "<h3>Populating Ideal Profiles for PTN</h3>";

$jurusan = $conn->query("SELECT id, kode FROM jurusan");
$kriteria = $conn->query("SELECT id, kode, aspek FROM kriteria")->fetch_all(MYSQLI_ASSOC);

while($j = $jurusan->fetch_assoc()) {
    echo "Processing " . $j['kode'] . "... <br>";
    foreach($kriteria as $k) {
        $nilai = 3.0; // Default
        
        // Logika sederhana untuk nilai ideal berdasarkan kode kriteria (K01-K12)
        if ($j['kode'] == 'ITB-STEI' || $j['kode'] == 'UI-SI') {
            if (in_array($k['kode'], ['K01', 'K04', 'K07', 'K10'])) $nilai = 5.0; // MTK, IPA, IT, Analitis
        } elseif ($j['kode'] == 'UI-FK') {
            if (in_array($k['kode'], ['K01', 'K04', 'K10'])) $nilai = 5.0; // MTK, IPA, Analitis
        } elseif ($j['kode'] == 'UGM-HK' || $j['kode'] == 'UNPAD-KOM') {
            if (in_array($k['kode'], ['K02', 'K03', 'K09', 'K11'])) $nilai = 5.0; // B.Indo, B.Ing, Sosial, Komunikasi
        } elseif ($j['kode'] == 'UNAIR-AK' || $j['kode'] == 'UB-MAN') {
            if (in_array($k['kode'], ['K01', 'K05', 'K06', 'K10'])) $nilai = 5.0; // MTK, IPS, Ekonomi, Analitis
        }
        
        $stmt = $conn->prepare("INSERT INTO profil_ideal (jurusan_id, kriteria_id, nilai_ideal) VALUES (?, ?, ?)");
        $stmt->bind_param('iid', $j['id'], $k['id'], $nilai);
        $stmt->execute();
    }
}
echo "Done! Profil Ideal populated.";
