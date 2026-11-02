<?php if (isset($_SESSION['user_id'])): ?>
            </main> <!-- End of content-body -->
        </div> <!-- End of main-wrapper -->
    </div> <!-- End of app-wrapper -->
<?php else: ?>
    </div> <!-- End of container main-content -->
    <footer class="footer">
        <div class="container">
            <p style="text-align: center;">&copy; <?php echo date('Y'); ?> MA Daarul Hikmah Pamulang. All rights reserved.</p>
        </div>
    </footer>
<?php endif; ?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Objek untuk memetakan status ke pesan
const statusMessages = {
    'success_tambah': { title: 'Berhasil!', text: 'Data berhasil ditambahkan.', icon: 'success' },
    'success_edit': { title: 'Berhasil!', text: 'Perubahan data berhasil disimpan.', icon: 'success' },
    'success_hapus': { title: 'Berhasil!', text: 'Data berhasil dihapus dari sistem.', icon: 'success' },
    'success': { title: 'Berhasil!', text: 'Aksi berhasil dilakukan.', icon: 'success' },
    'error': { title: 'Gagal!', text: 'Terjadi kesalahan sistem.', icon: 'error' },
    'error_hapus': { title: 'Gagal Hapus!', text: 'Data tidak bisa dihapus karena terkait data lain.', icon: 'error' },
    'login_success': { title: 'Selamat Datang!', text: 'Anda berhasil masuk ke sistem.', icon: 'success' },
    'success_hitung': { title: 'Perhitungan Selesai!', text: 'Hasil rekomendasi jurusan Anda telah diperbarui.', icon: 'success' },
    'error_hitung': { title: 'Gagal!', text: 'Data tidak lengkap untuk melakukan perhitungan.', icon: 'error' }
};

// Fungsi untuk menampilkan notifikasi otomatis dari URL
function checkStatus() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status && statusMessages[status]) {
        const msg = statusMessages[status];
        Swal.fire({
            title: msg.title,
            text: msg.text,
            icon: msg.icon,
            timer: 2000,
            showConfirmButton: false,
            customClass: {
                popup: 'swal2-rounded'
            }
        }).then(() => {
            // Bersihkan parameter status dari URL tanpa refresh
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        });
    }
}

function konfirmasiLogout(event) {
    event.preventDefault();
    const href = event.currentTarget.getAttribute('href');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Sesi Anda akan diakhiri dan Anda harus login kembali.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-rounded'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Anda telah keluar dari sistem.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'swal2-rounded'
                }
            }).then(() => {
                window.location.href = href;
            });
        }
    })
}

// Jalankan pengecekan status saat halaman dimuat
document.addEventListener('DOMContentLoaded', checkStatus);
</script>

<style>
/* Global CSS untuk SweetAlert agar konsisten dan tidak kaku */
.swal2-rounded {
    border-radius: 20px !important;
}
.swal2-confirm, .swal2-cancel {
    border-radius: 10px !important;
}
.swal2-title {
    font-family: 'Inter', sans-serif !important;
}
</style>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
