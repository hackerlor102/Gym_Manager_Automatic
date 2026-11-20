# THIẾT KẾ SƯỜN CODE HƯỚNG ĐỐI TƯỢNG (OOP TRONG PHP) - QUẢN LÝ PHÒNG GYM

Tài liệu này cung cấp thiết kế kiến trúc Lập trình Hướng đối tượng (Object-Oriented Programming - OOP) viết bằng **ngôn ngữ PHP**, kết nối với **Cơ sở dữ liệu MySQL trên XAMPP**, phục vụ 2 tác nhân chính: **Nhân viên (Staff)** và **Quản lý (Manager)**.

---

## 1. SƠ ĐỒ LỚP ĐỐI TƯỢNG (CLASS DIAGRAM)

```mermaid
classDiagram
    direction TB

    class Database {
        -static Database instance
        -PDO conn
        -construct()
        +static getInstance() Database
        +getConnection() PDO
    }

    class User {
        #int id
        #string maNV
        #string username
        #string password
        #string hoTen
        #string soDienThoai
        #string email
        #string vaiTro
        #string trangThai
        +kiemTraMatKhau(plainPassword) bool
        +isManager() bool
        +static login(username, password, pdo) User
        +static getById(id, pdo) User
    }

    class NhanVien {
        #string caLamViec
        #float luongCoBan
        #string ngayVaoLam
        +themHoiVien(data, pdo) int
        +capNhatHoiVien(id, data, pdo) bool
        +dangKyGoiTap(hoiVienId, goiTapId, phuongThuc, pdo) int
        +kiemTraCheckIn(hoiVienId, pdo) array
    }

    class QuanLy {
        #string phongBan
        #float phuCap
        +themNhanVien(data, pdo) int
        +capNhatNhanVien(id, data, pdo) bool
        +doiTrangThaiNhanVien(id, trangThai, pdo) bool
        +themGoiTap(data, pdo) int
        +capNhatGoiTap(id, data, pdo) bool
        +doiTrangThaiGoiTap(id, trangThai, pdo) bool
        +thongKeTongHop(pdo) array
    }

    class HoiVien {
        -int id
        -string maHV
        -string hoTen
        -string soDienThoai
        -string email
        -string gioiTinh
        -string ngayThamGia
        -int goiTapId
        -string tenGoiTap
        -string ngayHetHan
        -string trangThai
        +isGoiConHan() bool
        +soNgayConLai() int
        +tinhTrangThai() string
        +static getAll(pdo, keyword, status) array
        +static findByPhoneOrCode(search, pdo) HoiVien
    }

    class GoiTap {
        -int id
        -string maGoi
        -string tenGoi
        -int thoiHanThang
        -float giaTien
        -string moTa
        -string quyenLoi
        -string trangThai
        +getGiaFormatted() string
        +getQuyenLoiArray() array
        +isDangKinhDoanh() bool
        +static getAll(pdo, onlyActive) array
    }

    class HoaDon {
        -int id
        -string maHD
        -int hoiVienId
        -string tenHoiVien
        -int goiTapId
        -string tenGoiTap
        -float soTien
        -string phuongThuc
        -int nhanVienId
        -string tenNhanVien
        -string createdAt
        +getSoTienFormatted() string
        +getTenPhuongThuc() string
        +static getAll(pdo, keyword, method) array
    }

    class CheckIn {
        -int id
        -int hoiVienId
        -string maHV
        -string tenHoiVien
        -int nhanVienId
        -string tenNhanVien
        -string thoiGianCheckIn
        -bool hopLe
        -string ghiChu
        +getThoiGianFormatted() string
        +static getTodayLogs(pdo) array
    }

    User <|-- NhanVien : Kế thừa (Inheritance)
    NhanVien <|-- QuanLy : Kế thừa & Mở rộng quyền hạn
    NhanVien ..> HoiVien : Quản lý & Đăng ký gói
    NhanVien ..> HoaDon : Tạo hóa đơn
    NhanVien ..> CheckIn : Kiểm tra & ghi nhận
    QuanLy ..> NhanVien : Quản lý nhân sự
    QuanLy ..> GoiTap : Quản lý gói tập
    Database --> User : Cung cấp kết nối PDO
```

---

## 2. NGUYÊN TẮC OOP VÀ KIẾN TRÚC ÁP DỤNG TRONG PHP

### 2.1. Tính kế thừa (Inheritance)
- `User` là lớp cơ sở quản lý định danh người dùng hệ thống (`id`, `username`, `password`, `ho_ten`, `vai_tro`).
- `NhanVien extends User`: Kế thừa thuộc tính đăng nhập, bổ sung các nghiệp vụ thường trực tại quầy lễ tân:
  - Thêm, sửa, tìm kiếm hội viên (`themHoiVien`, `capNhatHoiVien`).
  - Điểm danh và kiểm tra hạn sử dụng thẻ (`kiemTraCheckIn`).
  - Đăng ký và gia hạn gói tập, xuất hóa đơn thu tiền (`dangKyGoiTap`).
- `QuanLy extends NhanVien`: Trong phòng gym, Quản lý có thể thực hiện mọi thao tác của Nhân viên, đồng thời được mở rộng thêm các thẩm quyền cấp cao:
  - Quản lý nhân sự (`themNhanVien`, `capNhatNhanVien`, `doiTrangThaiNhanVien`).
  - Quản lý danh mục gói tập (`themGoiTap`, `capNhatGoiTap`, `doiTrangThaiGoiTap`).
  - Báo cáo doanh thu và dòng tiền (`thongKeTongHop`).

### 2.2. Tính đóng gói (Encapsulation)
- Các trường dữ liệu nhạy cảm được đặt ở phạm vi `protected` hoặc `private`.
- Mọi logic nghiệp vụ (ví dụ: tự động tính toán ngày hết hạn, kiểm tra thẻ còn hạn hay không, định dạng chuỗi tiền tệ `number_format`) đều được đóng gói gọn trong Class tương ứng (`HoiVien`, `GoiTap`, `HoaDon`).

### 2.3. Mẫu thiết kế Singleton cho Database XAMPP
- Lớp `Database` trong [config/database.php](file:///d:/Code/Project%20x%C3%A0m%20lol/web%20qu%E1%BA%A3n%20l%C3%BD%20ph%C3%B2ng%20gym/config/database.php) áp dụng mẫu Singleton, bảo đảm toàn bộ hệ thống chỉ duy trì 1 kết nối PDO duy nhất đến MySQL, tiết kiệm tài nguyên và bảo đảm an toàn dữ liệu.

---

## 3. MAPPING CƠ SỞ DỮ LIỆU TRÊN XAMPP (MYSQL)

| Lớp OOP PHP | Bảng MySQL tương ứng | Khóa chính | Khóa ngoại |
|-------------|---------------------|------------|------------|
| `User`, `NhanVien`, `QuanLy` | `users` | `id` | - |
| `HoiVien` | `hoi_vien` | `id` | `goi_tap_id` -> `goi_tap(id)` |
| `GoiTap` | `goi_tap` | `id` | - |
| `HoaDon` | `hoa_don` | `id` | `hoi_vien_id`, `goi_tap_id`, `nhan_vien_id` |
| `CheckIn` | `check_in` | `id` | `hoi_vien_id`, `nhan_vien_id` |
