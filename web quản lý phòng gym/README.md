# 🏋️‍♂️ GYMFIT PRO - HỆ THỐNG QUẢN LÝ PHÒNG GYM (PHP & XAMPP)

Hệ thống quản lý phòng gym hoàn chỉnh xây dựng bằng **công nghệ PHP (Hướng đối tượng - OOP)**, sử dụng **Cơ sở dữ liệu MySQL trên XAMPP**, có cấu trúc phân quyền chặt chẽ giữa 2 tác nhân: **Nhân viên (Staff)** và **Quản lý (Manager)**.

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY TRÊN XAMPP

### Bước 1: Đặt thư mục dự án vào XAMPP
Sao chép thư mục dự án `web quản lý phòng gym` vào thư mục `htdocs` của XAMPP, ví dụ:
```
C:\xampp\htdocs\web_quan_ly_phong_gym
```

### Bước 2: Khởi động XAMPP Control Panel
1. Mở phần mềm **XAMPP Control Panel**.
2. Nhấn nút **Start** ở cả 2 dòng:
   * **Apache** (Chạy Web server PHP)
   * **MySQL** (Chạy Cơ sở dữ liệu)

### Bước 3: Cài đặt Cơ sở dữ liệu (Database)
Hệ thống có file kết nối riêng biệt tại: [config/database.php](file:///d:/Code/Project%20x%C3%A0m%20lol/web%20qu%E1%BA%A3n%20l%C3%BD%20ph%C3%B2ng%20gym/config/database.php). Bạn có 2 cách khởi tạo:
* **Cách 1 (Tự động - Khuyên dùng)**: Chỉ cần mở trình duyệt và truy cập trang web, file `config/database.php` sẽ tự động tạo database `gym_management` và nạp toàn bộ bảng từ file [database.sql](file:///d:/Code/Project%20x%C3%A0m%20lol/web%20qu%E1%BA%A3n%20l%C3%BD%20ph%C3%B2ng%20gym/database.sql).
* **Cách 2 (Thủ công qua phpMyAdmin)**:
  1. Truy cập: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
  2. Tạo mới Database tên: `gym_management` với bảng mã `utf8mb4_unicode_ci`.
  3. Chọn database `gym_management`, bấm tab **Import**, chọn file `database.sql` và nhấn **Import**.

### Bước 4: Truy cập trang web
Mở trình duyệt bất kỳ (Chrome, Edge, Cốc Cốc) và truy cập:
```
http://localhost/web_quan_ly_phong_gym/
```

---

## 🔐 BẮT BUỘC ĐĂNG NHẬP & PHÂN QUYỀN VAI TRÒ

Khi chưa đăng nhập, người dùng **luôn bị ràng buộc ở trang `login.php`**, không thể truy cập bất kỳ trang nghiệp vụ nào.

### 🔑 Tài khoản mẫu thử nghiệm sẵn có:
| Vai trò | Tên đăng nhập | Mật khẩu | Phân quyền & Chức năng |
|---------|---------------|----------|------------------------|
| 👑 **Quản Lý (Admin)** | `admin` | `123456` | Toàn quyền: Thống kê doanh thu, Quản lý nhân viên, Quản lý gói tập, Quản lý hội viên, Check-in, Hóa đơn |
| 👩‍💼 **Nhân Viên (Staff)** | `lananh` *(hoặc `quocbao`)* | `123456` | Cổng lễ tân: Kiểm tra check-in, Quản lý hội viên, Đăng ký gói tập, Lập hóa đơn *(Ẩn hoàn toàn chức năng quản trị)* |

*(Trên màn hình đăng nhập có sẵn 2 thẻ bấm nhanh để tự động điền và đăng nhập ngay).*

---

## 🎯 KHÔNG GIAN LÀM VIỆC THEO VAI TRÒ

### 1. Dành cho NHÂN VIÊN (Staff) - Giao diện Lễ Tân Quầy
* Khi đăng nhập thành công, tự động đưa nhân viên vào màn hình **Kiểm Tra Check-In**.
* **⏱️ Kiểm Tra Check-In**:
  * Tra cứu hội viên theo số điện thoại hoặc mã thẻ.
  * Hiển thị trạng thái màu xanh (Còn hạn sử dụng) hoặc màu đỏ (Đã hết hạn/Chưa có gói).
  * Nút "Xác nhận Check-in" lưu lịch sử điểm danh trực tiếp vào bảng `check_in` trong MySQL.
  * Nút "Gia hạn gói" mở popup gia hạn và lập hóa đơn ngay nếu thẻ đã hết hạn.
* **👥 Quản Lý Hội Viên & Đăng Ký Gói Tập**:
  * Danh sách khách hàng, tìm kiếm, thêm hội viên mới.
  * Đăng ký gói tập mới, gia hạn thêm tháng, chọn hình thức thanh toán (Tiền mặt, Chuyển khoản QR, Quẹt thẻ POS) và lưu hóa đơn vào bảng `hoa_don`.
* **🧾 Quản Lý Hóa Đơn**: Xem toàn bộ phiếu thu, xem chi tiết hóa đơn gym chuyên nghiệp và in ra giấy (`window.print`).
* **Bảo mật phân quyền**: Nhân viên hoàn toàn không thấy và không thể truy cập các trang quản trị (`dashboard.php`, `staff.php`, `packages.php`). Nếu cố ý gõ URL, hệ thống sẽ tự động chặn và chuyển hướng về trang được cấp quyền.

### 2. Dành cho QUẢN LÝ (Manager) - Giao diện Quản Trị Hệ Thống
* Khi đăng nhập thành công, tự động đưa Quản lý vào màn hình **Thống Kê Doanh Thu**.
* **📊 Thống Kê Doanh Thu & Vận Hành**:
  * 4 thẻ KPI: Tổng doanh thu, Tổng hội viên (Đang tập / Hết hạn), Lượt check-in hôm nay, Tổng số hóa đơn thu tiền.
  * Biểu đồ tỷ trọng doanh thu theo hình thức thanh toán (Tiền mặt / Chuyển khoản / Thẻ POS).
  * Bảng xếp hạng các gói tập bán chạy nhất theo số lượng đăng ký thực tế.
* **🧑‍💼 Quản Lý Nhân Viên**:
  * Danh sách nhân sự phòng gym, phân ca trực (Ca sáng, Ca chiều-tối, Hành chính).
  * Thêm nhân viên mới, điều chỉnh lương cơ bản, khóa/mở tài khoản nhân viên.
* **🏷️ Quản Lý Gói Tập**:
  * Cấu hình thời hạn (1 tháng, 3 tháng, 6 tháng, 1 năm, PT 1-1), giá niêm yết, danh sách quyền lợi (gym, yoga, sauna, PT).
  * Thêm gói tập mới, chỉnh sửa giá bán, tạm dừng kinh doanh hoặc mở bán lại.
* **👥 Toàn quyền hội viên, Check-in, Hóa đơn**: Quản lý có thể thao tác mọi chức năng như nhân viên.
* **🚪 Đăng Xuất (Logout)**: Nằm tại thanh Topbar góc trên bên phải, bấm vào để hủy session và quay về trang đăng nhập.

---

## 📂 CẤU TRÚC THƯ MỤC DỰ ÁN PHP

```
web quản lý phòng gym/
│
├── config/
│   └── database.php         # FILE KẾT NỐI RIÊNG BIỆT VỚI CSDL XAMPP (PDO MySQL)
│
├── database.sql             # Script SQL tạo bảng & nạp dữ liệu mẫu cho phpMyAdmin
│
├── classes/                 # CÁC LỚP HƯỚNG ĐỐI TƯỢNG (OOP PHP)
│   ├── User.php             # Lớp cơ sở Người dùng & Xác thực mật khẩu
│   ├── NhanVien.php         # Lớp Nhân viên kế thừa từ User
│   ├── QuanLy.php           # Lớp Quản lý kế thừa từ NhanVien & User
│   ├── HoiVien.php          # Lớp Hội viên (tự tính ngày hết hạn, trạng thái)
│   ├── GoiTap.php           # Lớp Gói tập (biểu phí, thời hạn, quyền lợi)
│   ├── HoaDon.php           # Lớp Hóa đơn thu tiền
│   └── CheckIn.php          # Lớp Lịch sử điểm danh check-in
│
├── includes/
│   ├── auth.php             # Kiểm tra session đăng nhập và phân quyền vai trò
│   ├── header.php           # Topbar, thông tin user, nút Đăng xuất
│   ├── sidebar.php          # Menu điều hướng lọc theo phân quyền
│   └── footer.php           # Modal dùng chung, toast thông báo, scripts
│
├── index.php                # Điều phối router: kiểm tra login và chuyển hướng
├── login.php                # Giao diện đăng nhập bắt buộc
├── logout.php               # Hủy session và đăng xuất
├── dashboard.php            # Trang Thống kê doanh thu (Chỉ Quản lý)
├── staff.php                # Trang Quản lý nhân viên (Chỉ Quản lý)
├── packages.php             # Trang Quản lý gói tập (Chỉ Quản lý)
├── members.php              # Trang Quản lý hội viên (Cả 2 vai trò)
├── checkin.php              # Trang Kiểm tra check-in (Cả 2 vai trò)
├── invoices.php             # Trang Quản lý hóa đơn & In ấn (Cả 2 vai trò)
├── action.php               # Controller xử lý POST actions (thêm/sửa qua OOP)
│
├── css/
│   └── style.css            # Tùy biến kiểu dáng và định dạng in ấn
├── oop_design.md            # Tài liệu thiết kế chi tiết sườn code OOP trong PHP
└── README.md                # Tài liệu hướng dẫn sử dụng
```
