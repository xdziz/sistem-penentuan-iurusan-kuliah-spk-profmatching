<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nama, password, role, nis FROM users WHERE email = ? OR nis = ?");
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nis'] = $user['nis'];

            $redirect = ($user['role'] === 'admin') ? 'admin/index.php?status=login_success' : 'user/dashboard.php?status=login_success';
            header('Location: ' . BASE_URL . $redirect);
            exit;
        }
    }

    header('Location: ' . BASE_URL . 'user/login.php?error=1');
    exit;
}
