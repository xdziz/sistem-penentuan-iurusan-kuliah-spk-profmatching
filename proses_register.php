<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $kelas = $_POST['kelas'];
    $jurusan_sma = $_POST['jurusan_sma'];

    $stmt = $conn->prepare("INSERT INTO users (nama, nis, email, password, kelas, jurusan_sma, role) VALUES (?, ?, ?, ?, ?, ?, 'siswa')");
    $stmt->bind_param('ssssss', $nama, $nis, $email, $password, $kelas, $jurusan_sma);

    if ($stmt->execute()) {
        header('Location: ' . BASE_URL . 'user/login.php?register=success');
    } else {
        header('Location: ' . BASE_URL . 'user/register.php?error=failed');
    }
    exit;
}