<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$aksi = $_GET['aksi'] ?? '';

if ($aksi === 'tambah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama_jurusan'];
    $fakultas = $_POST['fakultas'];
    $universitas = $_POST['universitas'];
    $akreditasi = $_POST['akreditasi'];
    $deskripsi = $_POST['deskripsi'];
    $prospek = $_POST['prospek_kerja'];

    $stmt = $conn->prepare("INSERT INTO jurusan (kode, nama_jurusan, fakultas, universitas, akreditasi, deskripsi, prospek_kerja) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssss', $kode, $nama, $fakultas, $universitas, $akreditasi, $deskripsi, $prospek);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/jurusan/index.php?status=success');
    } else {
        header('Location: ' . BASE_URL . 'admin/jurusan/tambah.php?status=error');
    }
}

elseif ($aksi === 'hapus') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM jurusan WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/jurusan/index.php?status=deleted');
    } else {
        header('Location: ' . BASE_URL . 'admin/jurusan/index.php?status=error');
    }
}

elseif ($aksi === 'edit') {
    $id = (int)$_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama_jurusan'];
    $fakultas = $_POST['fakultas'];
    $universitas = $_POST['universitas'];
    $akreditasi = $_POST['akreditasi'];
    $deskripsi = $_POST['deskripsi'];
    $prospek = $_POST['prospek_kerja'];

    $stmt = $conn->prepare("UPDATE jurusan SET kode=?, nama_jurusan=?, fakultas=?, universitas=?, akreditasi=?, deskripsi=?, prospek_kerja=? WHERE id=?");
    $stmt->bind_param('sssssssi', $kode, $nama, $fakultas, $universitas, $akreditasi, $deskripsi, $prospek, $id);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'admin/jurusan/index.php?status=updated');
    } else {
        header('Location: ' . BASE_URL . 'admin/jurusan/edit.php?id='.$id.'&status=error');
    }
}
exit;
