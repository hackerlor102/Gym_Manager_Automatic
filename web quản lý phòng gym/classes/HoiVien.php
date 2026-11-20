<?php
/**
 * LỚP: HoiVien (Hội viên phòng gym)
 * Đại diện cho khách hàng đăng ký tập luyện
 */
class HoiVien {
    private $id;
    private $maHV;
    private $hoTen;
    private $soDienThoai;
    private $email;
    private $gioiTinh;
    private $ngayThamGia;
    private $goiTapId;
    private $tenGoiTap;
    private $ngayHetHan;
    private $trangThai;
    private $avatar;

    public function __construct($data = []) {
        $this->id          = $data['id'] ?? null;
        $this->maHV        = $data['ma_hv'] ?? '';
        $this->hoTen       = $data['ho_ten'] ?? '';
        $this->soDienThoai = $data['so_dien_thoai'] ?? '';
        $this->email       = $data['email'] ?? '';
        $this->gioiTinh    = $data['gioi_tinh'] ?? 'Nam';
        $this->ngayThamGia = $data['ngay_tham_gia'] ?? date('Y-m-d');
        $this->goiTapId    = $data['goi_tap_id'] ?? null;
        $this->tenGoiTap   = $data['ten_goi'] ?? 'Chưa đăng ký gói';
        $this->ngayHetHan  = $data['ngay_het_han'] ?? null;
        $this->avatar      = $data['avatar'] ?? ('https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($this->hoTen));
        $this->trangThai   = $this->tinhTrangThai();
    }

    public function getId() { return $this->id; }
    public function getMaHV() { return $this->maHV; }
    public function getHoTen() { return $this->hoTen; }
    public function getSoDienThoai() { return $this->soDienThoai; }
    public function getEmail() { return $this->email; }
    public function getGioiTinh() { return $this->gioiTinh; }
    public function getNgayThamGia() { return $this->ngayThamGia; }
    public function getGoiTapId() { return $this->goiTapId; }
    public function getTenGoiTap() { return $this->tenGoiTap; }
    public function getNgayHetHan() { return $this->ngayHetHan; }
    public function getAvatar() { return $this->avatar; }
    public function getTrangThai() { return $this->trangThai; }

    /**
     * Kiểm tra gói tập còn hạn không
     */
    public function isGoiConHan() {
        if (empty($this->ngayHetHan)) return false;
        return strtotime($this->ngayHetHan) >= strtotime(date('Y-m-d'));
    }

    /**
     * Tính số ngày còn lại
     */
    public function soNgayConLai() {
        if (!$this->isGoiConHan()) return 0;
        $diff = strtotime($this->ngayHetHan) - strtotime(date('Y-m-d'));
        return max(0, (int)ceil($diff / 86400));
    }

    /**
     * Tự động tính trạng thái
     */
    public function tinhTrangThai() {
        if (empty($this->ngayHetHan)) return 'PENDING';
        return $this->isGoiConHan() ? 'ACTIVE' : 'EXPIRED';
    }

    /**
     * Lấy danh sách hội viên kèm tên gói tập
     */
    public static function getAll($pdo, $keyword = '', $status = 'ALL') {
        $sql = "
            SELECT hv.*, gt.ten_goi 
            FROM hoi_vien hv
            LEFT JOIN goi_tap gt ON hv.goi_tap_id = gt.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (hv.ho_ten LIKE :kw OR hv.so_dien_thoai LIKE :kw OR hv.ma_hv LIKE :kw)";
            $params[':kw'] = "%$keyword%";
        }

        $today = date('Y-m-d');
        if ($status === 'ACTIVE') {
            $sql .= " AND hv.ngay_het_han >= :today";
            $params[':today'] = $today;
        } elseif ($status === 'EXPIRED') {
            $sql .= " AND (hv.ngay_het_han < :today AND hv.ngay_het_han IS NOT NULL)";
            $params[':today'] = $today;
        } elseif ($status === 'PENDING') {
            $sql .= " AND hv.ngay_het_han IS NULL";
        }

        $sql .= " ORDER BY hv.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = new HoiVien($row);
        }
        return $list;
    }

    /**
     * Lấy theo ID
     */
    public static function getById($id, $pdo) {
        $stmt = $pdo->prepare("
            SELECT hv.*, gt.ten_goi 
            FROM hoi_vien hv
            LEFT JOIN goi_tap gt ON hv.goi_tap_id = gt.id
            WHERE hv.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new HoiVien($row) : null;
    }

    /**
     * Tìm theo SĐT hoặc Mã HV (phục vụ Check-in)
     */
    public static function findByPhoneOrCode($search, $pdo) {
        $stmt = $pdo->prepare("
            SELECT hv.*, gt.ten_goi 
            FROM hoi_vien hv
            LEFT JOIN goi_tap gt ON hv.goi_tap_id = gt.id
            WHERE hv.so_dien_thoai = :s OR hv.ma_hv = :s
            LIMIT 1
        ");
        $stmt->execute([':s' => $search]);
        $row = $stmt->fetch();
        return $row ? new HoiVien($row) : null;
    }
}
