<?php
require_once '../config/database.php';
session_start();
session_unset();
session_destroy();
header('Location: ' . BASE_URL . 'user/login.php');
exit;
