<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$aksi = $_GET['aksi'] ?? '';

if ($aksi === 'tambah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama_kriteria'];
    $aspek = $_POST['aspek'];
    $tipe = $_POST['tipe'];
    $keterangan = $_POST['keterangan'];

    $stmt = $conn->prepare("INSERT INTO kriteria (kode, nama_kriteria, aspek, tipe, keterangan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $kode, $nama, $aspek, $tipe, $keterangan);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/kriteria/index.php?status=success');
    } else {
        header('Location: ' . BASE_URL . 'admin/kriteria/tambah.php?status=error');
    }
}

elseif ($aksi === 'hapus') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM kriteria WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/kriteria/index.php?status=deleted');
    } else {
        header('Location: ' . BASE_URL . 'admin/kriteria/index.php?status=error');
    }
}

elseif ($aksi === 'edit') {
    $id = (int)$_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama_kriteria'];
    $aspek = $_POST['aspek'];
    $tipe = $_POST['tipe'];
    $keterangan = $_POST['keterangan'];

    $stmt = $conn->prepare("UPDATE kriteria SET kode=?, nama_kriteria=?, aspek=?, tipe=?, keterangan=? WHERE id=?");
    $stmt->bind_param('sssssi', $kode, $nama, $aspek, $tipe, $keterangan, $id);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/kriteria/index.php?status=updated');
    } else {
        header('Location: ' . BASE_URL . 'admin/kriteria/edit.php?id='.$id.'&status=error');
    }
}
exit;
