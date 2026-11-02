<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Ambil kriteria berdasarkan aspek
$kriteria_akademik = $conn->query("SELECT * FROM kriteria WHERE aspek = 'akademik' AND status = 1 ORDER BY kode ASC");
$kriteria_minat = $conn->query("SELECT * FROM kriteria WHERE aspek = 'minat' AND status = 1 ORDER BY kode ASC");
$kriteria_bakat = $conn->query("SELECT * FROM kriteria WHERE aspek = 'bakat' AND status = 1 ORDER BY kode ASC");
$kriteria_kepribadian = $conn->query("SELECT * FROM kriteria WHERE aspek = 'kepribadian' AND status = 1 ORDER BY kode ASC");
$kriteria_ekonomi = $conn->query("SELECT * FROM kriteria WHERE aspek = 'ekonomi' AND status = 1 ORDER BY kode ASC");

// Ambil data yang sudah pernah diisi (jika ada)
$profil_existing = [];
$pe = $conn->query("SELECT kriteria_id, nilai FROM siswa_profil WHERE user_id = $user_id");
while($row = $pe->fetch_assoc()) {
    $profil_existing[$row['kriteria_id']] = (int)$row['nilai'];
}

include '../includes/header.php';
?>

<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 2.1rem; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, #6366F1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.25rem;">
        Penilaian Mandiri
    </h1>
    <p style="color: var(--text-muted); font-size: 1.1rem; margin: 0;">Isi data berikut dengan jujur agar rekomendasi jurusan kuliah yang dihasilkan semakin akurat.</p>
</div>

<form action="../proses/proses_isi_data.php" method="POST">
    <div style="display: flex; flex-direction: column; gap: 2.5rem;">

        <!-- 1. Aspek Akademik (Nilai Rapor) -->
        <div class="card" style="border-top: 5px solid var(--primary);">
            <h3 style="font-size: 1.35rem; color: var(--primary); border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-chart-simple"></i> Aspek Akademik (Rata-rata Nilai Rapor)
            </h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem; background-color: var(--primary-light); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid var(--primary); line-height: 1.5;">
                Silakan konversikan rata-rata nilai mata pelajaran rapor Anda terakhir ke dalam skala 1–5: <br>
                <strong>90 - 100</strong> (Skala 5) | <strong>80 - 89</strong> (Skala 4) | <strong>70 - 79</strong> (Skala 3) | <strong>60 - 69</strong> (Skala 2) | <strong>&lt; 60</strong> (Skala 1)
            </p>
            
            <div style="display: flex; flex-direction: column; gap: 1.8rem;">
                <?php while($k = $kriteria_akademik->fetch_assoc()): ?>
                <div>
                    <label style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span style="background-color: var(--primary-light); color: var(--primary); font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo $k['kode']; ?></span>
                        <?php echo htmlspecialchars($k['nama_kriteria']); ?>
                    </label>
                    
                    <div class="scale-container">
                        <?php 
                        $scales = [
                            5 => ['label' => 'Sangat Baik', 'sub' => '90 - 100'],
                            4 => ['label' => 'Baik', 'sub' => '80 - 89'],
                            3 => ['label' => 'Cukup', 'sub' => '70 - 79'],
                            2 => ['label' => 'Kurang', 'sub' => '60 - 69'],
                            1 => ['label' => 'Sgt Kurang', 'sub' => '< 60']
                        ];
                        foreach($scales as $val => $opt):
                            $checked = (isset($profil_existing[$k['id']]) && $profil_existing[$k['id']] == $val) ? 'checked' : '';
                        ?>
                        <label class="scale-option" data-val="<?php echo $val; ?>">
                            <input type="radio" name="nilai[<?php echo $k['id']; ?>]" value="<?php echo $val; ?>" required <?php echo $checked; ?>>
                            <div class="scale-card">
                                <span class="scale-num"><?php echo $val; ?></span>
                                <span class="scale-label"><?php echo $opt['label']; ?></span>
                                <span style="font-size: 0.55rem; opacity: 0.8; font-weight: 600; margin-top: 0.15rem; display: block;"><?php echo $opt['sub']; ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- 2. Aspek Minat -->
        <div class="card" style="border-top: 5px solid #8B5CF6;">
            <h3 style="font-size: 1.35rem; color: #8B5CF6; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-heart"></i> Aspek Minat
            </h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem; background-color: rgba(139, 92, 246, 0.08); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #8B5CF6; line-height: 1.5;">
                Pilihlah skala ketertarikan Anda pada bidang-bidang berikut:<br>
                <strong>5</strong> = Sangat Menyukai | <strong>4</strong> = Menyukai | <strong>3</strong> = Netral | <strong>2</strong> = Kurang Menyukai | <strong>1</strong> = Sangat Tidak Menyukai
            </p>

            <div style="display: flex; flex-direction: column; gap: 1.8rem;">
                <?php while($k = $kriteria_minat->fetch_assoc()): ?>
                <div>
                    <label style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span style="background-color: rgba(139, 92, 246, 0.12); color: #8B5CF6; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo $k['kode']; ?></span>
                        <?php echo htmlspecialchars($k['nama_kriteria']); ?>
                        <?php if(!empty($k['keterangan'])): ?>
                            <span style="font-weight: 400; font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">— <?php echo htmlspecialchars($k['keterangan']); ?></span>
                        <?php endif; ?>
                    </label>
                    
                    <div class="scale-container">
                        <?php 
                        $scales_minat = [
                            5 => 'Sangat Suka',
                            4 => 'Suka',
                            3 => 'Netral',
                            2 => 'Kurang Suka',
                            1 => 'Tidak Suka'
                        ];
                        foreach($scales_minat as $val => $label):
                            $checked = (isset($profil_existing[$k['id']]) && $profil_existing[$k['id']] == $val) ? 'checked' : '';
                        ?>
                        <label class="scale-option" data-val="<?php echo $val; ?>">
                            <input type="radio" name="nilai[<?php echo $k['id']; ?>]" value="<?php echo $val; ?>" required <?php echo $checked; ?>>
                            <div class="scale-card">
                                <span class="scale-num"><?php echo $val; ?></span>
                                <span class="scale-label"><?php echo $label; ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- 3. Aspek Bakat -->
        <div class="card" style="border-top: 5px solid #EC4899;">
            <h3 style="font-size: 1.35rem; color: #EC4899; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-puzzle-piece"></i> Aspek Bakat / Potensi Diri
            </h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem; background-color: rgba(236, 72, 153, 0.08); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #EC4899; line-height: 1.5;">
                Pilihlah skala penguasaan atau kemampuan Anda pada bidang-bidang berikut:<br>
                <strong>5</strong> = Sangat Mampu/Menguasai | <strong>4</strong> = Mampu | <strong>3</strong> = Cukup Mampu | <strong>2</strong> = Kurang Mampu | <strong>1</strong> = Sangat Kurang Mampu
            </p>

            <div style="display: flex; flex-direction: column; gap: 1.8rem;">
                <?php while($k = $kriteria_bakat->fetch_assoc()): ?>
                <div>
                    <label style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span style="background-color: rgba(236, 72, 153, 0.12); color: #EC4899; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo $k['kode']; ?></span>
                        <?php echo htmlspecialchars($k['nama_kriteria']); ?>
                        <?php if(!empty($k['keterangan'])): ?>
                            <span style="font-weight: 400; font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">— <?php echo htmlspecialchars($k['keterangan']); ?></span>
                        <?php endif; ?>
                    </label>
                    
                    <div class="scale-container">
                        <?php 
                        $scales_bakat = [
                            5 => 'Sangat Mampu',
                            4 => 'Mampu',
                            3 => 'Cukup',
                            2 => 'Kurang Mampu',
                            1 => 'Tidak Mampu'
                        ];
                        foreach($scales_bakat as $val => $label):
                            $checked = (isset($profil_existing[$k['id']]) && $profil_existing[$k['id']] == $val) ? 'checked' : '';
                        ?>
                        <label class="scale-option" data-val="<?php echo $val; ?>">
                            <input type="radio" name="nilai[<?php echo $k['id']; ?>]" value="<?php echo $val; ?>" required <?php echo $checked; ?>>
                            <div class="scale-card">
                                <span class="scale-num"><?php echo $val; ?></span>
                                <span class="scale-label"><?php echo $label; ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- 4. Aspek Kepribadian -->
        <div class="card" style="border-top: 5px solid #10B981;">
            <h3 style="font-size: 1.35rem; color: #10B981; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-brain"></i> Aspek Kepribadian
            </h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem; background-color: rgba(16, 185, 129, 0.08); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #10B981; line-height: 1.5;">
                Pilihlah skala kesesuaian perilaku atau karakteristik diri Anda sehari-hari:<br>
                <strong>5</strong> = Sangat Sesuai/Sangat Baik | <strong>4</strong> = Sesuai/Baik | <strong>3</strong> = Netral/Cukup | <strong>2</strong> = Kurang Sesuai/Kurang | <strong>1</strong> = Sangat Tidak Sesuai/Buruk
            </p>

            <div style="display: flex; flex-direction: column; gap: 1.8rem;">
                <?php while($k = $kriteria_kepribadian->fetch_assoc()): ?>
                <div>
                    <label style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span style="background-color: rgba(16, 185, 129, 0.12); color: #10B981; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo $k['kode']; ?></span>
                        <?php echo htmlspecialchars($k['nama_kriteria']); ?>
                        <?php if(!empty($k['keterangan'])): ?>
                            <span style="font-weight: 400; font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">— <?php echo htmlspecialchars($k['keterangan']); ?></span>
                        <?php endif; ?>
                    </label>
                    
                    <div class="scale-container">
                        <?php 
                        $scales_kepribadian = [
                            5 => 'Sgt Sesuai',
                            4 => 'Sesuai',
                            3 => 'Netral',
                            2 => 'Kurang',
                            1 => 'Sgt Kurang'
                        ];
                        foreach($scales_kepribadian as $val => $label):
                            $checked = (isset($profil_existing[$k['id']]) && $profil_existing[$k['id']] == $val) ? 'checked' : '';
                        ?>
                        <label class="scale-option" data-val="<?php echo $val; ?>">
                            <input type="radio" name="nilai[<?php echo $k['id']; ?>]" value="<?php echo $val; ?>" required <?php echo $checked; ?>>
                            <div class="scale-card">
                                <span class="scale-num"><?php echo $val; ?></span>
                                <span class="scale-label"><?php echo $label; ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- 5. Aspek Sosial Ekonomi -->
        <div class="card" style="border-top: 5px solid #F59E0B;">
            <h3 style="font-size: 1.35rem; color: #F59E0B; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-wallet"></i> Aspek Sosial Ekonomi
            </h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem; background-color: rgba(245, 158, 11, 0.08); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #F59E0B; line-height: 1.5;">
                Pilihlah skala kemampuan dan kondisi finansial keluarga Anda:<br>
                Sesuaikan nominal penghasilan bulanan orang tua serta tingkat kesanggupan pembiayaan kuliah mandiri.
            </p>

            <div style="display: flex; flex-direction: column; gap: 1.8rem;">
                <?php while($k = $kriteria_ekonomi->fetch_assoc()): ?>
                <div>
                    <label style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span style="background-color: rgba(245, 158, 11, 0.12); color: #F59E0B; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo $k['kode']; ?></span>
                        <?php echo htmlspecialchars($k['nama_kriteria']); ?>
                        <?php if(!empty($k['keterangan'])): ?>
                            <span style="font-weight: 400; font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">— <?php echo htmlspecialchars($k['keterangan']); ?></span>
                        <?php endif; ?>
                    </label>
                    
                    <div class="scale-container">
                        <?php 
                        if ($k['kode'] === 'K24') {
                            // Penghasilan Orang Tua
                            $scales_ekonomi = [
                                5 => ['label' => 'Sgt Mampu', 'sub' => '> Rp 10 Juta'],
                                4 => ['label' => 'Mampu', 'sub' => 'Rp 5 - 10 Juta'],
                                3 => ['label' => 'Cukup', 'sub' => 'Rp 2.5 - 5 Juta'],
                                2 => ['label' => 'Kurang', 'sub' => 'Rp 1 - 2.5 Juta'],
                                1 => ['label' => 'Sgt Kurang', 'sub' => '< Rp 1 Juta']
                            ];
                        } else {
                            // Kesanggupan Biaya Kuliah
                            $scales_ekonomi = [
                                5 => ['label' => 'Sgt Tinggi', 'sub' => 'Mandiri + UKT Tinggi'],
                                4 => ['label' => 'Tinggi', 'sub' => 'Sanggup UKT Sedang-Tinggi'],
                                3 => ['label' => 'Sedang', 'sub' => 'Sanggup UKT Rendah-Sedang'],
                                2 => ['label' => 'Rendah', 'sub' => 'Hanya Sanggup UKT Rendah'],
                                1 => ['label' => 'Sgt Rendah', 'sub' => 'Butuh Beasiswa Penuh']
                            ];
                        }
                        
                        foreach($scales_ekonomi as $val => $opt):
                            $checked = (isset($profil_existing[$k['id']]) && $profil_existing[$k['id']] == $val) ? 'checked' : '';
                        ?>
                        <label class="scale-option" data-val="<?php echo $val; ?>">
                            <input type="radio" name="nilai[<?php echo $k['id']; ?>]" value="<?php echo $val; ?>" required <?php echo $checked; ?>>
                            <div class="scale-card">
                                <span class="scale-num"><?php echo $val; ?></span>
                                <span class="scale-label"><?php echo $opt['label']; ?></span>
                                <span style="font-size: 0.55rem; opacity: 0.8; font-weight: 600; margin-top: 0.15rem; display: block;"><?php echo $opt['sub']; ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

    </div>

    <!-- Submit Action Button -->
    <div style="margin-top: 3.5rem; display: flex; justify-content: center; margin-bottom: 2rem;">
        <button type="submit" class="btn btn-primary" style="padding: 1rem 3.5rem; font-size: 1.1rem; border-radius: var(--radius); gap: 0.75rem;">
            <i class="fa-solid fa-calculator"></i> Simpan & Hitung Rekomendasi
        </button>
    </div>
</form>

<?php include '../includes/footer.php'; ?>