<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $nilai_data = $_POST['nilai'];

    $conn->begin_transaction();

    try {
        foreach ($nilai_data as $kriteria_id => $nilai) {
            $kriteria_id = (int)$kriteria_id;
            $nilai = (float)$nilai;

            $stmt = $conn->prepare("INSERT INTO siswa_profil (user_id, kriteria_id, nilai) 
                                   VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
            $stmt->bind_param('iid', $user_id, $kriteria_id, $nilai);
            $stmt->execute();
        }

        $conn->commit();
        // Setelah simpan, langsung jalankan proses hitung
        header('Location: ' . BASE_URL . 'proses/proses_hitung.php');
    } catch (Exception $e) {
        $conn->rollback();
        header('Location: ' . BASE_URL . 'user/isi_data.php?status=error');
    }
}
exit;
