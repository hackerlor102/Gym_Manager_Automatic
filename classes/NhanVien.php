<?php
require_once __DIR__ . '/User.php';

/**
 * LỚP: NhanVien (Nhân viên lễ tân / vận hành)
 * Kế thừa từ User, chịu trách nhiệm các nghiệp vụ hàng ngày:
 * - Quản lý hội viên
 * - Đăng ký & gia hạn gói tập
 * - Kiểm tra check-in
 * - Quản lý hóa đơn thu tiền
 */
class NhanVien extends User {
    protected $caLamViec;
    protected $luongCoBan;
    protected $ngayVaoLam;

    public function __construct($data = []) {
        parent::__construct($data);
        $this->caLamViec  = $data['ca_lam_viec'] ?? 'Ca Sáng (06:00 - 14:00)';
        $this->luongCoBan = (float)($data['luong_co_ban'] ?? 7500000);
        $this->ngayVaoLam = $data['ngay_vao_lam'] ?? date('Y-m-d');
    }

    public function getCaLamViec() { return $this->caLamViec; }
    public function getLuongCoBan() { return $this->luongCoBan; }
    public function getNgayVaoLam() { return $this->ngayVaoLam; }

    public function getLuongFormatted() {
        return number_format($this->luongCoBan, 0, ',', '.') . ' đ';
    }

    /**
     * 1. Thêm hội viên mới
     */
    public function themHoiVien($data, $pdo) {
        $maHV = 'HV' . substr(time(), -4);
        $avatar = 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($data['ho_ten']);

        $stmt = $pdo->prepare("
            INSERT INTO hoi_vien (ma_hv, ho_ten, so_dien_thoai, email, gioi_tinh, ngay_tham_gia, trang_thai, avatar)
            VALUES (:ma_hv, :ho_ten, :sdt, :email, :gioi_tinh, CURRENT_DATE(), 'PENDING', :avatar)
        ");

        $stmt->execute([
            ':ma_hv'     => $maHV,
            ':ho_ten'    => $data['ho_ten'],
            ':sdt'       => $data['so_dien_thoai'],
            ':email'     => $data['email'] ?? '',
            ':gioi_tinh' => $data['gioi_tinh'] ?? 'Nam',
            ':avatar'    => $avatar
        ]);

        return $pdo->lastInsertId();
    }

    /**
     * 2. Cập nhật thông tin hội viên
     */
    public function capNhatHoiVien($id, $data, $pdo) {
        $stmt = $pdo->prepare("
            UPDATE hoi_vien 
            SET ho_ten = :ho_ten, so_dien_thoai = :sdt, email = :email, gioi_tinh = :gioi_tinh
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'        => $id,
            ':ho_ten'    => $data['ho_ten'],
            ':sdt'       => $data['so_dien_thoai'],
            ':email'     => $data['email'] ?? '',
            ':gioi_tinh' => $data['gioi_tinh'] ?? 'Nam'
        ]);
    }

    /**
     * 3. Đăng ký / Gia hạn gói tập & Lập hóa đơn
     */
    public function dangKyGoiTap($hoiVienId, $goiTapId, $phuongThuc, $pdo) {
        // Lấy thông tin hội viên hiện tại
        $stmtHV = $pdo->prepare("SELECT * FROM hoi_vien WHERE id = :id");
        $stmtHV->execute([':id' => $hoiVienId]);
        $hv = $stmtHV->fetch();
        if (!$hv) throw new Exception("Không tìm thấy hội viên!");

        // Lấy thông tin gói tập
        $stmtGT = $pdo->prepare("SELECT * FROM goi_tap WHERE id = :id");
        $stmtGT->execute([':id' => $goiTapId]);
        $gt = $stmtGT->fetch();
        if (!$gt) throw new Exception("Không tìm thấy gói tập!");

        // Tính ngày hết hạn mới
        $today = date('Y-m-d');
        $baseDate = $today;
        if (!empty($hv['ngay_het_han']) && $hv['ngay_het_han'] >= $today) {
            $baseDate = $hv['ngay_het_han']; // Còn hạn thì cộng dồn từ ngày hết hạn cũ
        }
        $thoiHanThang = (int)$gt['thoi_han_thang'];
        $newExpireDate = date('Y-m-d', strtotime("+$thoiHanThang month", strtotime($baseDate)));

        // Cập nhật hội viên
        $stmtUp = $pdo->prepare("
            UPDATE hoi_vien 
            SET goi_tap_id = :gt_id, ngay_het_han = :expire, trang_thai = 'ACTIVE'
            WHERE id = :id
        ");
        $stmtUp->execute([
            ':gt_id'  => $goiTapId,
            ':expire' => $newExpireDate,
            ':id'     => $hoiVienId
        ]);

        // Tạo hóa đơn mới
        $maHD = 'HD' . substr(time(), -6);
        $stmtHD = $pdo->prepare("
            INSERT INTO hoa_don (ma_hd, hoi_vien_id, goi_tap_id, so_tien, phuong_thuc, nhan_vien_id, ghi_chu, created_at)
            VALUES (:ma_hd, :hv_id, :gt_id, :so_tien, :phuong_thuc, :nv_id, :ghi_chu, NOW())
        ");
        $stmtHD->execute([
            ':ma_hd'       => $maHD,
            ':hv_id'       => $hoiVienId,
            ':gt_id'       => $goiTapId,
            ':so_tien'     => $gt['gia_tien'],
            ':phuong_thuc' => $phuongThuc,
            ':nv_id'       => $this->id,
            ':ghi_chu'     => "Đăng ký gói " . $gt['ten_goi'] . " (" . $thoiHanThang . " tháng)"
        ]);

        return $pdo->lastInsertId();
    }

    /**
     * 4. Kiểm tra check-in cho hội viên
     */
    public function kiemTraCheckIn($hoiVienId, $pdo) {
        $stmtHV = $pdo->prepare("SELECT * FROM hoi_vien WHERE id = :id");
        $stmtHV->execute([':id' => $hoiVienId]);
        $hv = $stmtHV->fetch();
        if (!$hv) throw new Exception("Hội viên không tồn tại!");

        $today = date('Y-m-d');
        $isHopLe = (!empty($hv['ngay_het_han']) && $hv['ngay_het_han'] >= $today);

        $ghiChu = $isHopLe 
            ? "Check-in thành công. Thẻ còn hạn sử dụng."
            : "Từ chối: Gói tập đã hết hạn sử dụng hoặc chưa đăng ký gói.";

        $stmtLog = $pdo->prepare("
            INSERT INTO check_in (hoi_vien_id, nhan_vien_id, thoi_gian_check_in, hop_le, ghi_chu)
            VALUES (:hv_id, :nv_id, NOW(), :hop_le, :ghi_chu)
        ");
        $stmtLog->execute([
            ':hv_id'   => $hoiVienId,
            ':nv_id'   => $this->id,
            ':hop_le'  => $isHopLe ? 1 : 0,
            ':ghi_chu' => $ghiChu
        ]);

        return [
            'hopLe'     => $isHopLe,
            'tenHoiVien'=> $hv['ho_ten'],
            'ghiChu'    => $ghiChu
        ];
    }
}
