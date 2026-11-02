<?php
// includes/auth.php

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'user/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'user/login.php');
        exit;
    }
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'user/dashboard.php');
        exit;
    }
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}
