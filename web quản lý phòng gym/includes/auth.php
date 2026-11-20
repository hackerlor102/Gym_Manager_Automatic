<?php
/**
 * AUTHENTICATION & SESSION GUARD
 * Quản lý phiên làm việc, kiểm tra đăng nhập và phân quyền truy cập
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/NhanVien.php';
require_once __DIR__ . '/../classes/QuanLy.php';
require_once __DIR__ . '/../classes/HoiVien.php';
require_once __DIR__ . '/../classes/GoiTap.php';
require_once __DIR__ . '/../classes/HoaDon.php';
require_once __DIR__ . '/../classes/CheckIn.php';

/**
 * Kiểm tra đã đăng nhập chưa, nếu chưa chuyển về login.php
 */
function checkLogin() {
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Kiểm tra quyền hạn vai trò (ví dụ: chỉ MANAGER được truy cập)
 */
function checkRole($allowedRoles = []) {
    checkLogin();
    $role = $_SESSION['user_role'] ?? '';
    if (!in_array($role, (array)$allowedRoles)) {
        // Nếu không có quyền, chuyển về trang mặc định của vai trò đó
        if ($role === 'STAFF') {
            header("Location: checkin.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    }
}

/**
 * Lấy đối tượng User (NhanVien hoặc QuanLy) đang đăng nhập hiện tại
 * @return QuanLy|NhanVien|null
 */
function getCurrentUser() {
    static $currentUser = null;
    if ($currentUser === null && !empty($_SESSION['user_id'])) {
        $pdo = getDB();
        $currentUser = User::getById($_SESSION['user_id'], $pdo);
    }
    return $currentUser;
}

/**
 * Đặt thông báo tạm thời (Flash message)
 */
function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type'    => $type
    ];
}

/**
 * Lấy và xóa thông báo tạm thời
 */
function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
