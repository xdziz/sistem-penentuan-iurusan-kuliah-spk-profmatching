<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cf = (int)$_POST['persen_cf'];
    $sf = (int)$_POST['persen_sf'];

    if (($cf + $sf) !== 100) {
        header('Location: ' . BASE_URL . 'admin/bobot/index.php?status=error_total');
        exit;
    }

    $stmt = $conn->prepare("UPDATE pengaturan_bobot SET persen_cf = ?, persen_sf = ? WHERE id = 1");
    $stmt->bind_param('ii', $cf, $sf);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/bobot/index.php?status=success');
    } else {
        header('Location: ' . BASE_URL . 'admin/bobot/index.php?status=error');
    }
}
exit;
