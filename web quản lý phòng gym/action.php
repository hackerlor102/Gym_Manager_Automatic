<?php
/**
 * CONTROLLER: action.php
 * Xử lý tập trung các thao tác thêm/sửa/xóa thông qua các phương thức của đối tượng OOP
 */
require_once __DIR__ . '/includes/auth.php';
checkLogin();

$act = $_GET['act'] ?? '';
$pdo = getDB();
$currentUser = getCurrentUser();

$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';

try {
    switch ($act) {
        // ==========================================
        // 1. NGHIỆP VỤ HỘI VIÊN (Staff & Manager)
        // ==========================================
        case 'add_member':
            $currentUser->themHoiVien($_POST, $pdo);
            setFlash("Thêm hội viên mới thành công!");
            header("Location: members.php");
            exit;

        case 'edit_member':
            $id = (int)($_POST['id'] ?? 0);
            $currentUser->capNhatHoiVien($id, $_POST, $pdo);
            setFlash("Cập nhật thông tin hội viên thành công!");
            header("Location: members.php");
            exit;

        // ==========================================
        // 2. ĐĂNG KÝ / GIA HẠN GÓI TẬP (Staff & Manager)
        // ==========================================
        case 'register_package':
            $hvId = (int)($_POST['hoi_vien_id'] ?? 0);
            $gtId = (int)($_POST['goi_tap_id'] ?? 0);
            $pt   = $_POST['phuong_thuc'] ?? 'TIEN_MAT';

            $currentUser->dangKyGoiTap($hvId, $gtId, $pt, $pdo);
            setFlash("Đăng ký / Gia hạn gói tập và xuất hóa đơn thành công!");
            header("Location: invoices.php");
            exit;

        // ==========================================
        // 3. CHECK-IN HỘI VIÊN (Staff & Manager)
        // ==========================================
        case 'do_checkin':
        case 'quick_checkin':
            $hvId = (int)($_POST['hoi_vien_id'] ?? 0);
            $result = $currentUser->kiemTraCheckIn($hvId, $pdo);

            if ($result['hopLe']) {
                setFlash("Check-in thành công cho " . $result['tenHoiVien'] . "!", "success");
            } else {
                setFlash("Từ chối check-in: " . $result['ghiChu'], "error");
            }
            header("Location: checkin.php");
            exit;

        // ==========================================
        // 4. QUẢN LÝ NHÂN VIÊN (Chỉ Manager)
        // ==========================================
        case 'add_staff':
            checkRole('MANAGER');
            $currentUser->themNhanVien($_POST, $pdo);
            setFlash("Thêm nhân viên mới thành công!");
            header("Location: staff.php");
            exit;

        case 'edit_staff':
            checkRole('MANAGER');
            $id = (int)($_POST['id'] ?? 0);
            $currentUser->capNhatNhanVien($id, $_POST, $pdo);
            setFlash("Cập nhật nhân viên thành công!");
            header("Location: staff.php");
            exit;

        case 'toggle_staff':
            checkRole('MANAGER');
            $id = (int)($_POST['id'] ?? 0);
            $st = $_POST['trang_thai'] ?? 'ACTIVE';
            $currentUser->doiTrangThaiNhanVien($id, $st, $pdo);
            setFlash("Đã thay đổi trạng thái nhân viên!");
            header("Location: staff.php");
            exit;

        // ==========================================
        // 5. QUẢN LÝ GÓI TẬP (Chỉ Manager)
        // ==========================================
        case 'add_package':
            checkRole('MANAGER');
            $currentUser->themGoiTap($_POST, $pdo);
            setFlash("Tạo gói tập mới thành công!");
            header("Location: packages.php");
            exit;

        case 'edit_package':
            checkRole('MANAGER');
            $id = (int)($_POST['id'] ?? 0);
            $currentUser->capNhatGoiTap($id, $_POST, $pdo);
            setFlash("Cập nhật gói tập thành công!");
            header("Location: packages.php");
            exit;

        case 'toggle_package':
            checkRole('MANAGER');
            $id = (int)($_POST['id'] ?? 0);
            $st = $_POST['trang_thai'] ?? 'ACTIVE';
            $currentUser->doiTrangThaiGoiTap($id, $st, $pdo);
            setFlash("Đã thay đổi trạng thái gói tập!");
            header("Location: packages.php");
            exit;

        default:
            header("Location: index.php");
            exit;
    }
} catch (Exception $e) {
    setFlash("Lỗi: " . $e->getMessage(), "error");
    header("Location: $referer");
    exit;
}
