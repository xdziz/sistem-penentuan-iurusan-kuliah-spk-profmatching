<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jurusan_id = (int)$_POST['jurusan_id'];
    $nilai_data = $_POST['nilai'];

    // Gunakan transaksi untuk keamanan
    $conn->begin_transaction();

    try {
        foreach ($nilai_data as $kriteria_id => $nilai) {
            $kriteria_id = (int)$kriteria_id;
            $nilai = (float)$nilai;

            // Update jika sudah ada, insert jika belum
            $stmt = $conn->prepare("INSERT INTO profil_ideal (jurusan_id, kriteria_id, nilai_ideal) 
                                   VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE nilai_ideal = VALUES(nilai_ideal)");
            $stmt->bind_param('iid', $jurusan_id, $kriteria_id, $nilai);
            $stmt->execute();
        }

        $conn->commit();
        header('Location: ' . BASE_URL . 'admin/profil_ideal/index.php?status=success');
    } catch (Exception $e) {
        $conn->rollback();
        header('Location: ' . BASE_URL . 'admin/profil_ideal/edit.php?id=' . $jurusan_id . '&status=error');
    }
}
exit;
