<?php
/**
 * LỚP: GoiTap (Gói tập luyện)
 * Đại diện cho các gói dịch vụ phòng gym cung cấp
 */
class GoiTap {
    private $id;
    private $maGoi;
    private $tenGoi;
    private $thoiHanThang;
    private $giaTien;
    private $moTa;
    private $quyenLoi;
    private $trangThai;

    public function __construct($data = []) {
        $this->id           = $data['id'] ?? null;
        $this->maGoi        = $data['ma_goi'] ?? '';
        $this->tenGoi       = $data['ten_goi'] ?? '';
        $this->thoiHanThang = (int)($data['thoi_han_thang'] ?? 1);
        $this->giaTien      = (float)($data['gia_tien'] ?? 0);
        $this->moTa         = $data['mo_ta'] ?? '';
        $this->quyenLoi     = $data['quyen_loi'] ?? '';
        $this->trangThai    = $data['trang_thai'] ?? 'ACTIVE';
    }

    public function getId() { return $this->id; }
    public function getMaGoi() { return $this->maGoi; }
    public function getTenGoi() { return $this->tenGoi; }
    public function getThoiHanThang() { return $this->thoiHanThang; }
    public function getGiaTien() { return $this->giaTien; }
    public function getMoTa() { return $this->moTa; }
    public function getQuyenLoiRaw() { return $this->quyenLoi; }
    public function getTrangThai() { return $this->trangThai; }

    public function isDangKinhDoanh() {
        return $this->trangThai === 'ACTIVE';
    }

    public function getGiaFormatted() {
        return number_format($this->giaTien, 0, ',', '.') . ' đ';
    }

    public function getQuyenLoiArray() {
        if (empty($this->quyenLoi)) return [];
        $lines = explode("\n", str_replace("\r", "", $this->quyenLoi));
        return array_filter(array_map('trim', $lines));
    }

    public static function getAll($pdo, $onlyActive = false) {
        $sql = "SELECT * FROM goi_tap";
        if ($onlyActive) {
            $sql .= " WHERE trang_thai = 'ACTIVE'";
        }
        $sql .= " ORDER BY id ASC";
        $stmt = $pdo->query($sql);
        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = new GoiTap($row);
        }
        return $list;
    }

    public static function getById($id, $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM goi_tap WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new GoiTap($row) : null;
    }
}
