<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rekomendasi Jurusan - MA Darul Hikmah</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- Theme Manager script to prevent dark mode flashing -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body class="<?php echo isset($_SESSION['user_id']) ? 'has-sidebar' : ''; ?>">

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo BASE_URL; ?>" class="logo-container" style="text-decoration: none !important; border: none !important; box-shadow: none !important; display: flex; align-items: center; outline: none !important; gap: 0.75rem;">
                    <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Logo" class="sidebar-logo" style="width: 30px !important; height: 30px !important;">
                    <div class="logo-text" style="text-decoration: none !important; line-height: 1.1;">
                        <span class="school-name" style="text-decoration: none !important; display: block; font-size: 0.9375rem !important;">MA Daarul Hikmah</span>
                        <span class="school-location" style="text-decoration: none !important; display: block; font-size: 0.65rem !important;">Pamulang</span>
                    </div>
                </a>
                <button class="sidebar-toggle" id="closeSidebar" style="display: none; padding: 0.25rem;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <nav class="sidebar-menu">
                <div class="menu-label">Menu Utama</div>
                
                <?php 
                $current_page = $_SERVER['REQUEST_URI'];
                function isActive($path) {
                    global $current_page;
                    return strpos($current_page, $path) !== false ? 'active' : '';
                }
                ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>admin/index.php" class="menu-item <?php echo isActive('admin/index.php'); ?>">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/siswa/index.php" class="menu-item <?php echo isActive('admin/siswa/'); ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Data Siswa</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/kelas/index.php" class="menu-item <?php echo isActive('admin/kelas/'); ?>">
                        <i class="fa-solid fa-school"></i>
                        <span>Data Kelas</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/jurusan/index.php" class="menu-item <?php echo isActive('admin/jurusan/'); ?>">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Data Jurusan</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/kriteria/index.php" class="menu-item <?php echo isActive('admin/kriteria/'); ?>">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Kriteria & Bobot</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/profil_ideal/index.php" class="menu-item <?php echo isActive('admin/profil_ideal/'); ?>">
                        <i class="fa-solid fa-bullseye"></i>
                        <span>Profil Ideal</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/bobot/index.php" class="menu-item <?php echo isActive('admin/bobot/'); ?>">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <span>Pengaturan Bobot</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>user/dashboard.php" class="menu-item <?php echo isActive('user/dashboard.php'); ?>">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>user/isi_data.php" class="menu-item <?php echo isActive('user/isi_data.php'); ?>">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Isi Data Penilaian</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>user/hasil.php" class="menu-item <?php echo isActive('user/hasil.php'); ?>">
                        <i class="fa-solid fa-award"></i>
                        <span>Hasil Rekomendasi</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>user/history.php" class="menu-item <?php echo isActive('user/history.php'); ?>">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Riwayat</span>
                    </a>
                <?php endif; ?>

                <div class="menu-label" style="margin-top: 1.5rem;">Lainnya</div>
                <a href="<?php echo BASE_URL; ?>panduan/index.php" class="menu-item <?php echo isActive('panduan/'); ?>">
                    <i class="fa-solid fa-book"></i>
                    <span>Panduan Sistem</span>
                </a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>laporan/index.php" class="menu-item <?php echo isActive('laporan/'); ?>">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Laporan Sistem</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Pengguna</div>
                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-dark); margin-bottom: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($_SESSION['nama']); ?>">
                    <?php echo htmlspecialchars($_SESSION['nama']); ?>
                </div>
                <a href="<?php echo BASE_URL; ?>proses/proses_logout.php" class="btn btn-outline btn-sm" style="width: 100%;" onclick="konfirmasiLogout(event)">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </a>
            </div>
        </aside>

        <div class="main-wrapper">
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div style="font-size: 0.875rem; color: var(--text-muted); font-weight: 600;" class="no-print">
                        <i class="fa-regular fa-calendar-days" style="margin-right: 0.4rem;"></i>
                        <?php 
                        $hari = [
                            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                        ];
                        $bulan = [
                            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                        ];
                        echo $hari[date('l')] . ', ' . date('d') . ' ' . $bulan[date('F')] . ' ' . date('Y');
                        ?>
                    </div>
                </div>
                <div class="top-bar-right no-print">
                    <!-- Light / Dark Mode Toggle Button -->
                    <button class="theme-toggle-btn" id="themeToggle" title="Ubah Tema" aria-label="Toggle Theme">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                </div>
            </header>
            <main class="content-body">
<?php else: ?>
    <!-- Navbar for Public Pages -->
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>" class="logo" style="display: flex; align-items: center; gap: 0.6rem;">
                <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Logo" style="width: 28px; height: 28px;">
                <span>MA Daarul Hikmah</span>
            </a>
            <ul class="nav-links">
                <li><a href="<?php echo BASE_URL; ?>user/login.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
                <li><a href="<?php echo BASE_URL; ?>user/register.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus"></i> Register</a></li>
            </ul>
        </div>
    </nav>
    <div class="container main-content" style="padding: 2.5rem 0;">
<?php endif; ?>
