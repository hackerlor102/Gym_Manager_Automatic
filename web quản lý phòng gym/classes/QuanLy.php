<?php
require_once __DIR__ . '/NhanVien.php';

/**
 * LỚP: QuanLy (Quản lý cấp cao phòng gym)
 * Kế thừa từ NhanVien (và User).
 * Có toàn bộ quyền của Nhân viên cộng thêm các quyền quản trị tối cao:
 * - Quản lý nhân viên (Thêm, sửa, phân ca, khóa/mở)
 * - Quản lý danh mục gói tập (Thêm, sửa giá, quyền lợi, dừng bán)
 * - Thống kê doanh thu & báo cáo tổng quan
 */
class QuanLy extends NhanVien {
    protected $phongBan;
    protected $phuCap;

    public function __construct($data = []) {
        parent::__construct($data);
        $this->vaiTro   = 'MANAGER';
        $this->phongBan = $data['phong_ban'] ?? 'Ban Quản Lý';
        $this->phuCap   = (float)($data['phu_cap'] ?? 3000000);
    }

    public function getPhongBan() { return $this->phongBan; }
    public function getPhuCap() { return $this->phuCap; }

    // ==========================================
    // 1. QUẢN LÝ NHÂN VIÊN
    // ==========================================

    public function themNhanVien($data, $pdo) {
        $maNV = 'NV' . substr(time(), -4);
        $plainPass = !empty($data['password']) ? $data['password'] : '123456';
        $hashedPass = password_hash($plainPass, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (ma_nv, username, password, ho_ten, so_dien_thoai, email, vai_tro, ca_lam_viec, luong_co_ban, ngay_vao_lam, trang_thai)
            VALUES (:ma_nv, :username, :password, :ho_ten, :sdt, :email, 'STAFF', :ca, :luong, CURRENT_DATE(), 'ACTIVE')
        ");

        $stmt->execute([
            ':ma_nv'    => $maNV,
            ':username' => $data['username'],
            ':password' => $hashedPass,
            ':ho_ten'   => $data['ho_ten'],
            ':sdt'      => $data['so_dien_thoai'],
            ':email'    => $data['email'] ?? '',
            ':ca'       => $data['ca_lam_viec'] ?? 'Ca Sáng (06:00 - 14:00)',
            ':luong'    => (float)($data['luong_co_ban'] ?? 7500000)
        ]);

        return $pdo->lastInsertId();
    }

    public function capNhatNhanVien($id, $data, $pdo) {
        $sql = "UPDATE users SET ho_ten = :ho_ten, so_dien_thoai = :sdt, ca_lam_viec = :ca, luong_co_ban = :luong WHERE id = :id";
        $params = [
            ':id'     => $id,
            ':ho_ten' => $data['ho_ten'],
            ':sdt'    => $data['so_dien_thoai'],
            ':ca'     => $data['ca_lam_viec'],
            ':luong'  => (float)$data['luong_co_ban']
        ];
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function doiTrangThaiNhanVien($id, $trangThai, $pdo) {
        $stmt = $pdo->prepare("UPDATE users SET trang_thai = :st WHERE id = :id AND vai_tro != 'MANAGER'");
        return $stmt->execute([':st' => $trangThai, ':id' => $id]);
    }

    public static function getAllStaff($pdo) {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    // ==========================================
    // 2. QUẢN LÝ GÓI TẬP
    // ==========================================

    public function themGoiTap($data, $pdo) {
        $maGoi = 'GT' . substr(time(), -3);
        $quyenLoi = is_array($data['quyen_loi']) ? implode("\n", $data['quyen_loi']) : $data['quyen_loi'];

        $stmt = $pdo->prepare("
            INSERT INTO goi_tap (ma_goi, ten_goi, thoi_han_thang, gia_tien, mo_ta, quyen_loi, trang_thai)
            VALUES (:ma, :ten, :thoi_han, :gia, :mo_ta, :quyen_loi, 'ACTIVE')
        ");
        $stmt->execute([
            ':ma'        => $maGoi,
            ':ten'       => $data['ten_goi'],
            ':thoi_han'  => (int)$data['thoi_han_thang'],
            ':gia'       => (float)$data['gia_tien'],
            ':mo_ta'     => $data['mo_ta'] ?? '',
            ':quyen_loi' => $quyenLoi
        ]);
        return $pdo->lastInsertId();
    }

    public function capNhatGoiTap($id, $data, $pdo) {
        $quyenLoi = is_array($data['quyen_loi']) ? implode("\n", $data['quyen_loi']) : $data['quyen_loi'];

        $stmt = $pdo->prepare("
            UPDATE goi_tap 
            SET ten_goi = :ten, thoi_han_thang = :thoi_han, gia_tien = :gia, mo_ta = :mo_ta, quyen_loi = :quyen_loi
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'        => $id,
            ':ten'       => $data['ten_goi'],
            ':thoi_han'  => (int)$data['thoi_han_thang'],
            ':gia'       => (float)$data['gia_tien'],
            ':mo_ta'     => $data['mo_ta'] ?? '',
            ':quyen_loi' => $quyenLoi
        ]);
    }

    public function doiTrangThaiGoiTap($id, $trangThai, $pdo) {
        $stmt = $pdo->prepare("UPDATE goi_tap SET trang_thai = :st WHERE id = :id");
        return $stmt->execute([':st' => $trangThai, ':id' => $id]);
    }

    // ==========================================
    // 3. THỐNG KÊ DOANH THU & HIỆU SUẤT VẬN HÀNH
    // ==========================================

    public function thongKeTongHop($pdo) {
        // 1. Tổng doanh thu
        $stmtRev = $pdo->query("SELECT COALESCE(SUM(so_tien), 0) AS total, COUNT(*) AS count_invoices FROM hoa_don");
        $revData = $stmtRev->fetch();
        $tongDoanhThu = (float)$revData['total'];
        $soHoaDon = (int)$revData['count_invoices'];

        // 2. Doanh thu theo phương thức thanh toán
        $stmtMethods = $pdo->query("
            SELECT phuong_thuc, COALESCE(SUM(so_tien), 0) as amount 
            FROM hoa_don 
            GROUP BY phuong_thuc
        ");
        $theoPhuongThuc = ['TIEN_MAT' => 0, 'CHUYEN_KHOAN' => 0, 'THE' => 0];
        while ($row = $stmtMethods->fetch()) {
            if (isset($theoPhuongThuc[$row['phuong_thuc']])) {
                $theoPhuongThuc[$row['phuong_thuc']] = (float)$row['amount'];
            }
        }

        // 3. Thống kê hội viên
        $today = date('Y-m-d');
        $stmtMembers = $pdo->query("SELECT COUNT(*) AS total FROM hoi_vien");
        $tongHoiVien = (int)$stmtMembers->fetch()['total'];

        $stmtActiveMembers = $pdo->prepare("SELECT COUNT(*) AS total FROM hoi_vien WHERE ngay_het_han >= :today");
        $stmtActiveMembers->execute([':today' => $today]);
        $hoiVienConHan = (int)$stmtActiveMembers->fetch()['total'];
        $hoiVienHetHan = $tongHoiVien - $hoiVienConHan;

        // 4. Lượt check-in hôm nay
        $stmtCheckin = $pdo->prepare("SELECT COUNT(*) AS total FROM check_in WHERE DATE(thoi_gian_check_in) = :today");
        $stmtCheckin->execute([':today' => $today]);
        $checkInHomNay = (int)$stmtCheckin->fetch()['total'];

        // 5. Thống kê gói tập bán chạy
        $stmtTopPacks = $pdo->query("
            SELECT gt.ten_goi, COUNT(hd.id) AS sales_count, SUM(hd.so_tien) AS total_revenue
            FROM hoa_don hd
            JOIN goi_tap gt ON hd.goi_tap_id = gt.id
            GROUP BY hd.goi_tap_id
            ORDER BY sales_count DESC
            LIMIT 5
        ");
        $topPackages = $stmtTopPacks->fetchAll();

        return [
            'tongDoanhThu'   => $tongDoanhThu,
            'soHoaDon'       => $soHoaDon,
            'theoPhuongThuc' => $theoPhuongThuc,
            'tongHoiVien'    => $tongHoiVien,
            'hoiVienConHan'  => $hoiVienConHan,
            'hoiVienHetHan'  => $hoiVienHetHan,
            'checkInHomNay'  => $checkInHomNay,
            'topPackages'    => $topPackages
        ];
    }
}
