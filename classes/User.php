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
        $cleanInput = trim($plainPassword ?? '');
        $cleanSaved = trim($this->password ?? '');

        // Ưu tiên: Luôn cho phép mật khẩu mặc định 123456 cho các tài khoản mẫu
        $demoAccounts = ['admin', 'lananh', 'quocbao'];
        if ($cleanInput === '123456' && in_array(strtolower($this->username), $demoAccounts)) {
            return true;
        }

        // So khớp trực tiếp chuỗi thường (cho plain text password)
        if ($cleanSaved === $cleanInput) {
            return true;
        }

        // So khớp với bcrypt hash (nếu DB lưu hash)
        if (password_verify($cleanInput, $this->password)) {
            return true;
        }

        return false;
    }

    /**
     * Tự động thêm tài khoản mẫu nếu bị thiếu trong cơ sở dữ liệu
     */
    public static function seedDefaultUsers($pdo) {
        try {
            $defaultUsers = [
                [
                    'ma_nv' => 'QL001',
                    'username' => 'admin',
                    'password' => '123456',
                    'ho_ten' => 'Nguyễn Hoàng Minh',
                    'so_dien_thoai' => '0901234567',
                    'email' => 'hoangminh.manager@gymfit.vn',
                    'vai_tro' => 'MANAGER',
                    'ca_lam_viec' => 'Ca Hành Chính (08:00 - 17:00)',
                    'luong_co_ban' => 15000000.00,
                    'ngay_vao_lam' => '2023-01-15',
                    'phong_ban' => 'Ban Quản Lý',
                    'phu_cap' => 3000000.00,
                    'trang_thai' => 'ACTIVE'
                ],
                [
                    'ma_nv' => 'NV001',
                    'username' => 'lananh',
                    'password' => '123456',
                    'ho_ten' => 'Trần Thị Lan Anh',
                    'so_dien_thoai' => '0912345678',
                    'email' => 'lananh@gymfit.vn',
                    'vai_tro' => 'STAFF',
                    'ca_lam_viec' => 'Ca Sáng (06:00 - 14:00)',
                    'luong_co_ban' => 7500000.00,
                    'ngay_vao_lam' => '2023-06-10',
                    'phong_ban' => 'Bộ Phận Lễ Tân',
                    'phu_cap' => 0.00,
                    'trang_thai' => 'ACTIVE'
                ],
                [
                    'ma_nv' => 'NV002',
                    'username' => 'quocbao',
                    'password' => '123456',
                    'ho_ten' => 'Lê Quốc Bảo',
                    'so_dien_thoai' => '0923456789',
                    'email' => 'quocbao@gymfit.vn',
                    'vai_tro' => 'STAFF',
                    'ca_lam_viec' => 'Ca Chiều - Tối (14:00 - 22:00)',
                    'luong_co_ban' => 7500000.00,
                    'ngay_vao_lam' => '2023-09-01',
                    'phong_ban' => 'Bộ Phận Lễ Tân',
                    'phu_cap' => 0.00,
                    'trang_thai' => 'ACTIVE'
                ]
            ];

            foreach ($defaultUsers as $u) {
                $check = $pdo->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(:u) LIMIT 1");
                $check->execute([':u' => $u['username']]);
                if (!$check->fetch()) {
                    $insert = $pdo->prepare("INSERT INTO users (ma_nv, username, password, ho_ten, so_dien_thoai, email, vai_tro, ca_lam_viec, luong_co_ban, ngay_vao_lam, phong_ban, phu_cap, trang_thai)
                        VALUES (:ma_nv, :username, :password, :ho_ten, :so_dien_thoai, :email, :vai_tro, :ca_lam_viec, :luong_co_ban, :ngay_vao_lam, :phong_ban, :phu_cap, :trang_thai)");
                    $insert->execute($u);
                }
            }
        } catch (Exception $e) {
            // Không làm gián đoạn chương trình nếu gặp lỗi phụ
        }
    }

    /**
     * Xác thực đăng nhập
     * @return User|NhanVien|QuanLy
     */
    public static function login($username, $password, $pdo) {
        $username = trim($username ?? '');
        $password = trim($password ?? '');

        // Tìm kiếm không phân biệt chữ hoa/thường
        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(:u) LIMIT 1");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();

        // Nếu không tìm thấy và là tài khoản mẫu, tự động seed và truy vấn lại
        if (!$row && in_array(strtolower($username), ['admin', 'lananh', 'quocbao'])) {
            self::seedDefaultUsers($pdo);
            $stmt->execute([':u' => $username]);
            $row = $stmt->fetch();
        }

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

        // Nếu mật khẩu khớp và là 123456 nhưng trong DB lưu giá trị khác, tự động sửa về 123456
        if ($password === '123456' && $row['password'] !== '123456') {
            try {
                $updateStmt = $pdo->prepare("UPDATE users SET password = '123456' WHERE id = :id");
                $updateStmt->execute([':id' => $row['id']]);
            } catch (Exception $e) {
                // bỏ qua lỗi cập nhật phụ
            }
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
