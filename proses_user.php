<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

if ($aksi === 'tambah') {
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];

    // Validasi Nama Lengkap: hanya boleh huruf dan spasi
    if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
        header('Location: ../admin/siswa/tambah.php?status=error_nama_invalid');
        exit;
    }

    // Validasi NIS: hanya boleh angka
    if (!preg_match('/^[0-9]+$/', $nis)) {
        header('Location: ../admin/siswa/tambah.php?status=error_nis_invalid');
        exit;
    }

    $email = $_POST['email'];
    $kelas = $_POST['kelas'];
    $jurusan_sma = $_POST['jurusan_sma'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (nama, nis, email, kelas, jurusan_sma, password, role) VALUES (?, ?, ?, ?, ?, ?, 'siswa')");
    $stmt->bind_param('ssssss', $nama, $nis, $email, $kelas, $jurusan_sma, $password);
    
    if ($stmt->execute()) {
        header('Location: ../admin/siswa/index.php?status=success_tambah');
    } else {
        header('Location: ../admin/siswa/tambah.php?status=error');
    }
}

elseif ($aksi === 'edit') {
    $id = (int)$_POST['id'];
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];

    // Validasi Nama Lengkap: hanya boleh huruf dan spasi
    if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
        header('Location: ../admin/siswa/edit.php?id=' . $id . '&status=error_nama_invalid');
        exit;
    }

    // Validasi NIS: hanya boleh angka
    if (!preg_match('/^[0-9]+$/', $nis)) {
        header('Location: ../admin/siswa/edit.php?id=' . $id . '&status=error_nis_invalid');
        exit;
    }

    $email = $_POST['email'];
    $kelas = $_POST['kelas'];
    $jurusan_sma = $_POST['jurusan_sma'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET nama=?, nis=?, email=?, kelas=?, jurusan_sma=?, password=? WHERE id=? AND role='siswa'");
        $stmt->bind_param('ssssssi', $nama, $nis, $email, $kelas, $jurusan_sma, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, nis=?, email=?, kelas=?, jurusan_sma=? WHERE id=? AND role='siswa'");
        $stmt->bind_param('sssssi', $nama, $nis, $email, $kelas, $jurusan_sma, $id);
    }

    if ($stmt->execute()) {
        header('Location: ../admin/siswa/index.php?status=success_edit');
    } else {
        header('Location: ../admin/siswa/edit.php?id=' . $id . '&status=error');
    }
}

elseif ($aksi === 'hapus') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'siswa'");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header('Location: ../admin/siswa/index.php?status=success_hapus');
    } else {
        header('Location: ../admin/siswa/index.php?status=error_hapus');
    }
}

else {
    header('Location: ../admin/siswa/index.php');
}
exit;
