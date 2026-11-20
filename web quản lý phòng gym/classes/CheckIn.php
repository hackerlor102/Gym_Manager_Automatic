<?php
/**
 * LỚP: CheckIn (Lịch sử điểm danh check-in vào phòng tập)
 */
class CheckIn {
    private $id;
    private $hoiVienId;
    private $maHV;
    private $tenHoiVien;
    private $soDienThoai;
    private $nhanVienId;
    private $tenNhanVien;
    private $thoiGianCheckIn;
    private $hopLe;
    private $ghiChu;

    public function __construct($data = []) {
        $this->id              = $data['id'] ?? null;
        $this->hoiVienId       = $data['hoi_vien_id'] ?? null;
        $this->maHV            = $data['ma_hv'] ?? '';
        $this->tenHoiVien      = $data['ten_hoi_vien'] ?? ($data['ho_ten'] ?? '');
        $this->soDienThoai     = $data['so_dien_thoai'] ?? '';
        $this->nhanVienId      = $data['nhan_vien_id'] ?? null;
        $this->tenNhanVien     = $data['ten_nhan_vien'] ?? '';
        $this->thoiGianCheckIn = $data['thoi_gian_check_in'] ?? date('Y-m-d H:i:s');
        $this->hopLe           = (bool)($data['hopLe'] ?? ($data['hop_le'] ?? true));
        $this->ghiChu          = $data['ghi_chu'] ?? '';
    }

    public function getId() { return $this->id; }
    public function getHoiVienId() { return $this->hoiVienId; }
    public function getMaHV() { return $this->maHV; }
    public function getTenHoiVien() { return $this->tenHoiVien; }
    public function getSoDienThoai() { return $this->soDienThoai; }
    public function getNhanVienId() { return $this->nhanVienId; }
    public function getTenNhanVien() { return $this->tenNhanVien; }
    public function getThoiGianCheckIn() { return $this->thoiGianCheckIn; }
    public function isHopLe() { return $this->hopLe; }
    public function getGhiChu() { return $this->ghiChu; }

    public function getThoiGianFormatted() {
        return date('H:i:s d/m/Y', strtotime($this->thoiGianCheckIn));
    }

    public static function getTodayLogs($pdo) {
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("
            SELECT ck.*, hv.ma_hv, hv.ho_ten AS ten_hoi_vien, hv.so_dien_thoai, u.ho_ten AS ten_nhan_vien
            FROM check_in ck
            JOIN hoi_vien hv ON ck.hoi_vien_id = hv.id
            JOIN users u ON ck.nhan_vien_id = u.id
            WHERE DATE(ck.thoi_gian_check_in) = :today
            ORDER BY ck.id DESC
        ");
        $stmt->execute([':today' => $today]);

        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = new CheckIn($row);
        }
        return $list;
    }
}
