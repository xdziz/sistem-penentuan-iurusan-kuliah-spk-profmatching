<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$jurusan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$jurusan_id) {
    header('Location: hasil.php');
    exit;
}

// 1. Ambil info jurusan
$qj = $conn->prepare("SELECT * FROM jurusan WHERE id = ?");
$qj->bind_param('i', $jurusan_id);
$qj->execute();
$jurusan = $qj->get_result()->fetch_assoc();

// 2. Ambil profil siswa
$profil_siswa = [];
$qs = $conn->prepare("SELECT kriteria_id, nilai FROM siswa_profil WHERE user_id = ?");
$qs->bind_param('i', $user_id);
$qs->execute();
$rs = $qs->get_result();
while ($row = $rs->fetch_assoc()) {
    $profil_siswa[$row['kriteria_id']] = (float)$row['nilai'];
}

// 3. Ambil profil ideal jurusan & kriteria
$qk = $conn->prepare("
    SELECT k.id, k.kode, k.nama_kriteria, k.tipe, k.aspek, pi.nilai_ideal 
    FROM kriteria k
    JOIN profil_ideal pi ON k.id = pi.kriteria_id
    WHERE pi.jurusan_id = ?
    ORDER BY k.aspek ASC, k.kode ASC
");
$qk->bind_param('i', $jurusan_id);
$qk->execute();
$rk = $qk->get_result();
$kriteria_data = $rk->fetch_all(MYSQLI_ASSOC);

// 4. Ambil tabel bobot gap
$tabel_bobot = [];
$qb = $conn->query("SELECT gap, bobot FROM bobot_gap");
while ($row = $qb->fetch_assoc()) {
    $tabel_bobot[(int)$row['gap']] = (float)$row['bobot'];
}

function getBobot(int $gap, array $tabel): float {
    return isset($tabel[$gap]) ? $tabel[$gap] : 1.0;
}

// 5. Ambil setting persen
$qs = $conn->query("SELECT persen_cf, persen_sf FROM pengaturan_bobot LIMIT 1");
$setting = $qs->fetch_assoc();
$p_cf = $setting['persen_cf'] / 100;
$p_sf = $setting['persen_sf'] / 100;

include '../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <a href="hasil.php" style="text-decoration: none; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">← Kembali ke Hasil</a>
    <h1 class="card-title" style="font-size: 1.5rem; margin-top: 1rem;">Rincian Perhitungan: <?php echo $jurusan['nama_jurusan']; ?></h1>
    <p style="color: var(--text-muted); font-size: 0.875rem;">Step-by-step perhitungan Profile Matching untuk kebutuhan dokumentasi skripsi.</p>
</div>

<!-- STEP 1: Pemetaan Gap -->
<div class="card" style="margin-bottom: 2rem; padding: 0; overflow: hidden;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
        <h3 style="margin: 0; font-size: 1rem;">Langkah 1: Pemetaan Gap & Konversi Bobot</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8125rem;">
        <thead>
            <tr style="background-color: var(--bg-light); border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem;">Kriteria</th>
                <th style="padding: 1rem; text-align: center;">Profil Siswa (S)</th>
                <th style="padding: 1rem; text-align: center;">Profil Ideal (I)</th>
                <th style="padding: 1rem; text-align: center;">Gap (S - I)</th>
                <th style="padding: 1rem; text-align: center; color: var(--primary);">Bobot Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $cf_scores = [];
            $sf_scores = [];
            foreach($kriteria_data as $k): 
                $nilai_s = $profil_siswa[$k['id']] ?? 0;
                $nilai_i = (float)$k['nilai_ideal'];
                $gap = (int)($nilai_s - $nilai_i);
                $bobot = getBobot($gap, $tabel_bobot);
                
                if ($k['tipe'] == 'CF') $cf_scores[] = $bobot;
                else $sf_scores[] = $bobot;
            ?>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 1rem;">
                    <div style="font-weight: 600;"><?php echo $k['nama_kriteria']; ?></div>
                    <div style="font-size: 0.7rem; color: var(--text-muted);"><?php echo $k['kode']; ?> | <?php echo $k['tipe']; ?></div>
                </td>
                <td style="padding: 1rem; text-align: center;"><?php echo $nilai_s; ?></td>
                <td style="padding: 1rem; text-align: center;"><?php echo $nilai_i; ?></td>
                <td style="padding: 1rem; text-align: center;"><?php echo ($gap > 0 ? '+' : '') . $gap; ?></td>
                <td style="padding: 1rem; text-align: center; font-weight: 700; color: var(--primary); background: rgba(37,99,235,0.02);"><?php echo $bobot; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- STEP 2: NCF & NSF -->
    <div class="card">
        <h3 style="margin-top: 0; font-size: 1rem; margin-bottom: 1.5rem;">Langkah 2: Menghitung NCF & NSF</h3>
        
        <div style="margin-bottom: 1.5rem;">
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem;">CORE FACTOR (NCF)</div>
            <div style="font-size: 0.875rem; line-height: 1.6;">
                Σ Bobot (<?php echo implode(' + ', $cf_scores); ?>) / <?php echo count($cf_scores); ?> <br>
                <strong>NCF = <?php echo number_format(array_sum($cf_scores)/count($cf_scores), 4); ?></strong>
            </div>
        </div>

        <div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem;">SECONDARY FACTOR (NSF)</div>
            <div style="font-size: 0.875rem; line-height: 1.6;">
                Σ Bobot (<?php echo implode(' + ', $sf_scores); ?>) / <?php echo count($sf_scores); ?> <br>
                <strong>NSF = <?php echo number_format(array_sum($sf_scores)/count($sf_scores), 4); ?></strong>
            </div>
        </div>
    </div>

    <!-- STEP 3: TOTAL -->
    <div class="card" style="background: var(--bg-light); border-style: dashed;">
        <h3 style="margin-top: 0; font-size: 1rem; margin-bottom: 1.5rem;">Langkah 3: Nilai Total (Skor Akhir)</h3>
        
        <?php 
        $ncf = array_sum($cf_scores)/count($cf_scores);
        $nsf = array_sum($sf_scores)/count($sf_scores);
        $total = ($p_cf * $ncf) + ($p_sf * $nsf);
        ?>

        <div style="font-size: 0.875rem; line-height: 2;">
            Rumus: (<?php echo $setting['persen_cf']; ?>% x NCF) + (<?php echo $setting['persen_sf']; ?>% x NSF) <br>
            Hitung: (<?php echo $p_cf; ?> x <?php echo number_format($ncf, 4); ?>) + (<?php echo $p_sf; ?> x <?php echo number_format($nsf, 4); ?>) <br>
            Hasil: <?php echo number_format($p_cf * $ncf, 4); ?> + <?php echo number_format($p_sf * $nsf, 4); ?> <br>
            <div style="margin-top: 1rem; padding: 1rem; background: var(--white); border: 1px solid var(--border); border-radius: 6px;">
                <span style="color: var(--text-muted);">SKOR AKHIR =</span> 
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);"><?php echo number_format($total, 4); ?></span>
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 2rem; padding: 1.5rem; background: #FFFBEB; border-radius: 8px; border: 1px solid #FEF3C7; font-size: 0.875rem; color: #92400E;">
    <strong>Catatan Skripsi:</strong> Perhitungan di atas mengikuti tabel standar Profile Matching. Gap dihitung dengan rumus (Siswa - Ideal). Hasil positif (+) berarti kelebihan kompetensi, hasil negatif (-) berarti kekurangan kompetensi.
</div>

<?php include '../includes/footer.php'; ?>
