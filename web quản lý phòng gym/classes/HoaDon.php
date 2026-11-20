<?php
/**
 * LỚP: HoaDon (Hóa đơn thu tiền gói tập)
 * Đại diện cho giao dịch đăng ký hoặc gia hạn thẻ tập
 */
class HoaDon {
    private $id;
    private $maHD;
    private $hoiVienId;
    private $tenHoiVien;
    private $goiTapId;
    private $tenGoiTap;
    private $soTien;
    private $phuongThuc;
    private $nhanVienId;
    private $tenNhanVien;
    private $ghiChu;
    private $createdAt;

    public function __construct($data = []) {
        $this->id          = $data['id'] ?? null;
        $this->maHD        = $data['ma_hd'] ?? '';
        $this->hoiVienId   = $data['hoi_vien_id'] ?? null;
        $this->tenHoiVien  = $data['ten_hoi_vien'] ?? ($data['ho_ten'] ?? '');
        $this->goiTapId    = $data['goi_tap_id'] ?? null;
        $this->tenGoiTap   = $data['ten_goi'] ?? '';
        $this->soTien      = (float)($data['so_tien'] ?? 0);
        $this->phuongThuc  = $data['phuong_thuc'] ?? 'TIEN_MAT';
        $this->nhanVienId  = $data['nhan_vien_id'] ?? null;
        $this->tenNhanVien = $data['ten_nhan_vien'] ?? '';
        $this->ghiChu      = $data['ghi_chu'] ?? '';
        $this->createdAt   = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId() { return $this->id; }
    public function getMaHD() { return $this->maHD; }
    public function getHoiVienId() { return $this->hoiVienId; }
    public function getTenHoiVien() { return $this->tenHoiVien; }
    public function getGoiTapId() { return $this->goiTapId; }
    public function getTenGoiTap() { return $this->tenGoiTap; }
    public function getSoTien() { return $this->soTien; }
    public function getPhuongThuc() { return $this->phuongThuc; }
    public function getNhanVienId() { return $this->nhanVienId; }
    public function getTenNhanVien() { return $this->tenNhanVien; }
    public function getGhiChu() { return $this->ghiChu; }
    public function getCreatedAt() { return $this->createdAt; }

    public function getSoTienFormatted() {
        return number_format($this->soTien, 0, ',', '.') . ' đ';
    }

    public function getTenPhuongThuc() {
        $map = [
            'TIEN_MAT'     => 'Tiền mặt',
            'CHUYEN_KHOAN' => 'Chuyển khoản QR',
            'THE'          => 'Thẻ POS'
        ];
        return $map[$this->phuongThuc] ?? $this->phuongThuc;
    }

    public function getNgayTaoFormatted() {
        return date('d/m/Y H:i', strtotime($this->createdAt));
    }

    public static function getAll($pdo, $keyword = '', $method = 'ALL') {
        $sql = "
            SELECT hd.*, hv.ho_ten AS ten_hoi_vien, gt.ten_goi, u.ho_ten AS ten_nhan_vien
            FROM hoa_don hd
            LEFT JOIN hoi_vien hv ON hd.hoi_vien_id = hv.id
            LEFT JOIN goi_tap gt ON hd.goi_tap_id = gt.id
            LEFT JOIN users u ON hd.nhan_vien_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (hd.ma_hd LIKE :kw OR hv.ho_ten LIKE :kw OR gt.ten_goi LIKE :kw)";
            $params[':kw'] = "%$keyword%";
        }

        if ($method !== 'ALL') {
            $sql .= " AND hd.phuong_thuc = :m";
            $params[':m'] = $method;
        }

        $sql .= " ORDER BY hd.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = new HoaDon($row);
        }
        return $list;
    }

    public static function getById($id, $pdo) {
        $stmt = $pdo->prepare("
            SELECT hd.*, hv.ho_ten AS ten_hoi_vien, gt.ten_goi, u.ho_ten AS ten_nhan_vien
            FROM hoa_don hd
            LEFT JOIN hoi_vien hv ON hd.hoi_vien_id = hv.id
            LEFT JOIN goi_tap gt ON hd.goi_tap_id = gt.id
            LEFT JOIN users u ON hd.nhan_vien_id = u.id
            WHERE hd.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new HoaDon($row) : null;
    }
}
