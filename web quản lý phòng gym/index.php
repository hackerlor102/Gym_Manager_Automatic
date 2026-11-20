<?php
/**
 * TRANG ĐIỀU PHỐI CHÍNH (INDEX.PHP)
 * Chuyển hướng người dùng dựa theo trạng thái đăng nhập và phân quyền vai trò
 */
require_once __DIR__ . '/includes/auth.php';

// 1. Nếu chưa đăng nhập -> Buộc phải sang login.php
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Nếu đã đăng nhập -> Chuyển về đúng trang mặc định theo vai trò
$role = $_SESSION['user_role'] ?? 'STAFF';

if ($role === 'MANAGER') {
    header("Location: dashboard.php");
} else {
    header("Location: checkin.php");
}
exit;
