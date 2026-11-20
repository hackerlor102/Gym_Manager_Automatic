<?php
/**
 * LỚP CƠ SỞ: User (Người dùng hệ thống)
 * Quản lý thông tin tài khoản và xác thực mật khẩu
 */
class User {
    protected $id;
    protected $maNV;
    protected $username;
    protected $password;
    protected $hoTen;
    protected $soDienThoai;
    protected $email;
    protected $vaiTro;      // 'MANAGER' | 'STAFF'
    protected $trangThai;   // 'ACTIVE' | 'INACTIVE'

    public function __construct($data = []) {
        $this->id          = $data['id'] ?? null;
        $this->maNV        = $data['ma_nv'] ?? '';
        $this->username    = $data['username'] ?? '';
        $this->password    = $data['password'] ?? '';
        $this->hoTen       = $data['ho_ten'] ?? '';
        $this->soDienThoai = $data['so_dien_thoai'] ?? '';
        $this->email       = $data['email'] ?? '';
        $this->vaiTro      = $data['vai_tro'] ?? 'STAFF';
        $this->trangThai   = $data['trang_thai'] ?? 'ACTIVE';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getMaNV() { return $this->maNV; }
    public function getUsername() { return $this->username; }
    public function getHoTen() { return $this->hoTen; }
    public function getSoDienThoai() { return $this->soDienThoai; }
    public function getEmail() { return $this->email; }
    public function getVaiTro() { return $this->vaiTro; }
    public function getTrangThai() { return $this->trangThai; }

    public function isManager() {
        return $this->vaiTro === 'MANAGER';
    }

    /**
     * Kiểm tra mật khẩu (hỗ trợ cả hash bcrypt lẫn plain text demo)
     */
    public function kiemTraMatKhau($plainPassword) {
        if (password_verify($plainPassword, $this->password)) {
            return true;
        }
        return ($this->password === $plainPassword);
    }

    /**
     * Xác thực đăng nhập
     * @return User|NhanVien|QuanLy
     */
    public static function login($username, $password, $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("Tên đăng nhập không tồn tại!");
        }

        if ($row['trang_thai'] !== 'ACTIVE') {
            throw new Exception("Tài khoản này hiện đang bị khóa hoặc tạm nghỉ việc!");
        }

        // Tạo instance tương ứng
        $user = ($row['vai_tro'] === 'MANAGER') ? new QuanLy($row) : new NhanVien($row);

        if (!$user->kiemTraMatKhau($password)) {
            throw new Exception("Mật khẩu không chính xác!");
        }

        return $user;
    }

    /**
     * Lấy User theo ID
     */
    public static function getById($id, $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return ($row['vai_tro'] === 'MANAGER') ? new QuanLy($row) : new NhanVien($row);
    }
}
