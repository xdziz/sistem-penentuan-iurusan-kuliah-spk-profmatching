<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
requireAdmin();

// Ambil pengaturan bobot CF/SF
$q = $conn->query("SELECT * FROM pengaturan_bobot LIMIT 1");
$pengaturan = $q->fetch_assoc();

// Ambil tabel bobot gap
$gap_list = $conn->query("SELECT * FROM bobot_gap ORDER BY gap DESC");

include '../../includes/header.php';
?>

<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 2.1rem; font-weight: 800; background: linear-gradient(135deg, var(--primary) 0%, #6366F1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.25rem;">
        <i class="fa-solid fa-scale-balanced"></i> Pengaturan Bobot & Gap
    </h1>
    <p style="color: var(--text-muted); font-size: 1.05rem; margin: 0;">Konfigurasi persentase aspek penilaian Profile Matching dan tabel konversi nilai gap secara interaktif.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2.5rem; align-items: start;">
    
    <!-- Form Pengaturan Persentase -->
    <div class="card" style="background: var(--bg-card); border-radius: 20px; box-shadow: var(--shadow-lg); padding: 2.2rem; border: 1px solid var(--border); border-top: 5px solid var(--primary);">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-percent" style="color: var(--primary); font-size: 1.25rem;"></i>
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--text-dark); font-weight: 700;">
                Persentase Aspek
            </h3>
        </div>
        
        <form action="../../proses/proses_bobot.php" method="POST" id="weightForm">
            
            <!-- Visual Split Progress Bar -->
            <div style="margin-bottom: 1.8rem;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; display: block; text-transform: uppercase; letter-spacing: 0.05em;">Visualisasi Pembagian Bobot</label>
                <div class="visual-split-bar" style="display: flex; height: 26px; border-radius: 13px; overflow: hidden; background: var(--bg-light); border: 1px solid var(--border); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                    <div id="bar_cf" class="split-cf" style="background: linear-gradient(90deg, var(--primary) 0%, #4F46E5 100%); height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 800; transition: width 0.3s ease;">60%</div>
                    <div id="bar_sf" class="split-sf" style="background: linear-gradient(90deg, #8B5CF6 0%, #EC4899 100%); height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 800; transition: width 0.3s ease;">40%</div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600; color: var(--text-muted);">
                    <span>Core Factor: <strong id="val_cf" style="color: var(--primary);">60%</strong></span>
                    <span>Secondary Factor: <strong id="val_sf" style="color: #8B5CF6;">40%</strong></span>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.4rem;">
                <label for="persen_cf" style="color: var(--text-dark); font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Core Factor (CF) %</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="number" name="persen_cf" id="persen_cf" value="<?php echo $pengaturan['persen_cf']; ?>" min="0" max="100" style="padding-right: 2rem;" required>
                    <span style="position: absolute; right: 1rem; font-weight: 600; color: var(--text-muted);">%</span>
                </div>
                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Persentase kontribusi faktor utama (akademik/IT) dalam nilai akhir.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="persen_sf" style="color: var(--text-dark); font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Secondary Factor (SF) %</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="number" name="persen_sf" id="persen_sf" value="<?php echo $pengaturan['persen_sf']; ?>" min="0" max="100" style="padding-right: 2rem;" required>
                    <span style="position: absolute; right: 1rem; font-weight: 600; color: var(--text-muted);">%</span>
                </div>
                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Persentase kontribusi faktor pendukung (minat/kepribadian/ekonomi) dalam nilai akhir.</small>
            </div>
            
            <div style="padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); border-left: 4px solid var(--primary); margin-bottom: 1.8rem;">
                <p style="margin: 0; font-size: 0.85rem; color: var(--primary); font-weight: 600; line-height: 1.4; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-circle-info"></i> Jumlah total persentase CF dan SF akan otomatis disesuaikan agar selalu bernilai 100%.
                </p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 10px; font-weight: 700; padding: 0.9rem 1.5rem;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
            </button>
        </form>
    </div>

    <!-- Tabel Bobot Gap -->
    <div class="card" style="padding: 0; overflow: hidden; background: var(--bg-card); border-radius: 20px; box-shadow: var(--shadow-lg); border: 1px solid var(--border);">
        <div style="padding: 1.6rem 2rem; border-bottom: 1px solid var(--border); background: var(--bg-light); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-table-list" style="color: var(--primary); font-size: 1.25rem;"></i>
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--text-dark); font-weight: 700;">Tabel Konversi Nilai Gap</h3>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                    <th style="padding: 1rem 2rem; font-weight: 700; color: var(--text-muted);">NILAI GAP</th>
                    <th style="padding: 1rem 2rem; font-weight: 700; color: var(--text-muted); text-align: center;">BOBOT NILAI</th>
                    <th style="padding: 1rem 2rem; font-weight: 700; color: var(--text-muted);">KETERANGAN/STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php while($g = $gap_list->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" class="gap-row">
                    <td style="padding: 1rem 2rem; font-weight: 800; color: var(--text-dark); font-size: 1.05rem;">
                        <?php echo $g['gap']; ?>
                    </td>
                    <td style="padding: 1rem 2rem; text-align: center;">
                        <span style="background: var(--primary-light); color: var(--primary); padding: 0.35rem 0.9rem; border-radius: 20px; font-weight: 800; font-size: 0.9rem; display: inline-block; min-width: 60px;">
                            <?php echo number_format($g['bobot'], 1); ?>
                        </span>
                    </td>
                    <td style="padding: 1rem 2rem;">
                        <?php 
                        $status_class = '';
                        $status_icon = '';
                        $status_label = '';
                        
                        if ($g['gap'] == 0) {
                            $status_color = '#10B981'; // Green
                            $status_bg = 'rgba(16, 185, 129, 0.1)';
                            $status_icon = '<i class="fa-solid fa-circle-check"></i>';
                        } elseif ($g['gap'] > 0) {
                            $status_color = '#3B82F6'; // Blue
                            $status_bg = 'rgba(59, 130, 246, 0.1)';
                            $status_icon = '<i class="fa-solid fa-angle-up"></i>';
                        } else {
                            $status_color = '#F59E0B'; // Orange
                            $status_bg = 'rgba(245, 158, 11, 0.1)';
                            $status_icon = '<i class="fa-solid fa-angle-down"></i>';
                        }
                        ?>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="background: <?php echo $status_bg; ?>; color: <?php echo $status_color; ?>; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                <?php echo $status_icon; ?>
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.82rem; font-weight: 500;">
                                <?php echo htmlspecialchars($g['keterangan']); ?>
                            </span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div style="padding: 1.25rem 2rem; background: rgba(245, 158, 11, 0.08); font-size: 0.8rem; color: #b45309; border-top: 1px solid var(--border); display: flex; align-items: center; gap: 0.6rem; line-height: 1.5;">
            <i class="fa-solid fa-lock" style="font-size: 0.9rem;"></i>
            <span>* Tabel gap mengikuti standar algoritma Profile Matching dan bersifat <strong>read-only</strong> di halaman ini.</span>
        </div>
    </div>
</div>

<!-- Autocomplete and sync weights logic -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const cfInput = document.getElementById('persen_cf');
    const sfInput = document.getElementById('persen_sf');
    const barCf = document.getElementById('bar_cf');
    const barSf = document.getElementById('bar_sf');
    const valCf = document.getElementById('val_cf');
    const valSf = document.getElementById('val_sf');

    function syncWeights(source) {
        let cfVal = parseInt(cfInput.value) || 0;
        let sfVal = parseInt(sfInput.value) || 0;

        if (cfVal > 100) { cfVal = 100; cfInput.value = 100; }
        if (cfVal < 0) { cfVal = 0; cfInput.value = 0; }
        if (sfVal > 100) { sfVal = 100; sfInput.value = 100; }
        if (sfVal < 0) { sfVal = 0; sfInput.value = 0; }

        if (source === 'cf') {
            sfVal = 100 - cfVal;
            sfInput.value = sfVal;
        } else {
            cfVal = 100 - sfVal;
            cfInput.value = cfVal;
        }

        // Update progress bar
        barCf.style.width = cfVal + '%';
        barSf.style.width = sfVal + '%';

        // Update labels inside the bar
        barCf.textContent = cfVal >= 15 ? cfVal + '%' : '';
        barSf.textContent = sfVal >= 15 ? sfVal + '%' : '';

        // Update text labels
        valCf.textContent = cfVal + '%';
        valSf.textContent = sfVal + '%';
    }

    cfInput.addEventListener('input', () => syncWeights('cf'));
    sfInput.addEventListener('input', () => syncWeights('sf'));

    // Trigger initial sync to set visual elements correctly
    syncWeights('cf');
});
</script>

<style>
/* CSS enhancements for clean table rendering and dark theme compatibility */
.gap-row:hover {
    background-color: var(--bg-light) !important;
}
input[type="number"] {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2.2px solid var(--border);
    border-radius: 10px;
    background-color: var(--bg-light);
    color: var(--text-dark);
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.2s ease;
    outline: none;
}
input[type="number"]:focus {
    border-color: var(--primary);
    background-color: var(--bg-card);
    box-shadow: 0 0 0 3px var(--primary-light);
}
</style>

<?php include '../../includes/footer.php'; ?>