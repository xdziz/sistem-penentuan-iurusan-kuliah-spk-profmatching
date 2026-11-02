/**
 * Script Interaktivitas Utama - SPK Jurusan MA Darul Hikmah
 * Terintegrasi: Dark/Light Mode, Sidebar Mobile Toggle, SweetAlert Confirms
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initMobileSidebar();
});

/**
 * ── LOGIKA TEMA (DARK/LIGHT MODE) ──
 */
function initTheme() {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;

    // Fungsi update icon di tombol toggle
    const updateToggleIcon = (theme) => {
        const icon = themeToggle.querySelector('i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fa-solid fa-sun';
                themeToggle.title = 'Ubah ke Mode Terang';
            } else {
                icon.className = 'fa-solid fa-moon';
                themeToggle.title = 'Ubah ke Mode Gelap';
            }
        }
    };

    // Ambil theme saat ini dari html tag (diset di head blocking script)
    let currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateToggleIcon(currentTheme);

    // Event listener click toggle
    themeToggle.addEventListener('click', () => {
        const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', nextTheme);
        localStorage.setItem('theme', nextTheme);
        currentTheme = nextTheme;
        updateToggleIcon(currentTheme);
    });
}

/**
 * ── LOGIKA SIDEBAR RESPONSIVE (LAYAR PONSEL/TABLET) ──
 */
function initMobileSidebar() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('closeSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const sidebar = document.getElementById('sidebar');

    if (!sidebar) return;

    const openSidebar = () => {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        if (closeBtn) closeBtn.style.display = 'block';
    };

    const closeSidebar = () => {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        if (closeBtn) closeBtn.style.display = 'none';
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', openSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Auto close sidebar when resizing screen to desktop width
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            closeSidebar();
            if (closeBtn) closeBtn.style.display = 'none';
        }
    });
}
