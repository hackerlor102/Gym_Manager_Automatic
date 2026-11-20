-- ========================================================
-- CƠ SỞ DỮ LIỆU: GYMFIT PRO - HỆ THỐNG QUẢN LÝ PHÒNG GYM
-- Tương thích: MySQL / MariaDB (XAMPP)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `gym_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gym_management`;

-- 1. BẢNG USERS (Quản lý & Nhân viên)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_nv` VARCHAR(50) UNIQUE NOT NULL,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `ho_ten` VARCHAR(100) NOT NULL,
    `so_dien_thoai` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NULL,
    `vai_tro` ENUM('MANAGER', 'STAFF') NOT NULL DEFAULT 'STAFF',
    `ca_lam_viec` VARCHAR(100) DEFAULT 'Ca Sáng (06:00 - 14:00)',
    `luong_co_ban` DECIMAL(15, 2) DEFAULT 7500000.00,
    `ngay_vao_lam` DATE NULL,
    `phong_ban` VARCHAR(100) DEFAULT 'Ban Quản Lý',
    `phu_cap` DECIMAL(15, 2) DEFAULT 0.00,
    `trang_thai` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG GÓI TẬP (Gym Packages)
DROP TABLE IF EXISTS `goi_tap`;
CREATE TABLE `goi_tap` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_goi` VARCHAR(50) UNIQUE NOT NULL,
    `ten_goi` VARCHAR(150) NOT NULL,
    `thoi_han_thang` INT NOT NULL DEFAULT 1,
    `gia_tien` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `mo_ta` TEXT NULL,
    `quyen_loi` TEXT NULL,
    `trang_thai` ENUM('ACTIVE', 'DISABLED') DEFAULT 'ACTIVE',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG HỘI VIÊN (Gym Members)
DROP TABLE IF EXISTS `hoi_vien`;
CREATE TABLE `hoi_vien` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_hv` VARCHAR(50) UNIQUE NOT NULL,
    `ho_ten` VARCHAR(100) NOT NULL,
    `so_dien_thoai` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NULL,
    `gioi_tinh` ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam',
    `ngay_tham_gia` DATE NOT NULL,
    `goi_tap_id` INT NULL,
    `ngay_het_han` DATE NULL,
    `trang_thai` ENUM('ACTIVE', 'EXPIRED', 'PENDING') DEFAULT 'PENDING',
    `avatar` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`goi_tap_id`) REFERENCES `goi_tap`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG HÓA ĐƠN THU TIỀN (Invoices)
DROP TABLE IF EXISTS `hoa_don`;
CREATE TABLE `hoa_don` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_hd` VARCHAR(50) UNIQUE NOT NULL,
    `hoi_vien_id` INT NOT NULL,
    `goi_tap_id` INT NOT NULL,
    `so_tien` DECIMAL(15, 2) NOT NULL,
    `phuong_thuc` ENUM('TIEN_MAT', 'CHUYEN_KHOAN', 'THE') DEFAULT 'TIEN_MAT',
    `nhan_vien_id` INT NOT NULL,
    `ghi_chu` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`hoi_vien_id`) REFERENCES `hoi_vien`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`goi_tap_id`) REFERENCES `goi_tap`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`nhan_vien_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG CHECK-IN HỘI VIÊN (CheckIn Logs)
DROP TABLE IF EXISTS `check_in`;
CREATE TABLE `check_in` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `hoi_vien_id` INT NOT NULL,
    `nhan_vien_id` INT NOT NULL,
    `thoi_gian_check_in` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `hop_le` TINYINT(1) DEFAULT 1,
    `ghi_chu` VARCHAR(255) NULL,
    FOREIGN KEY (`hoi_vien_id`) REFERENCES `hoi_vien`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`nhan_vien_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- DỮ LIỆU MẪU BAN ĐẦU (Mật khẩu chuẩn: 123456)
-- ========================================================

-- 1. Users mẫu
INSERT INTO `users` (`id`, `ma_nv`, `username`, `password`, `ho_ten`, `so_dien_thoai`, `email`, `vai_tro`, `ca_lam_viec`, `luong_co_ban`, `ngay_vao_lam`, `phong_ban`, `phu_cap`, `trang_thai`) VALUES
(1, 'QL001', 'admin', '123456', 'Nguyễn Hoàng Minh', '0901234567', 'hoangminh.manager@gymfit.vn', 'MANAGER', 'Ca Hành Chính (08:00 - 17:00)', 15000000.00, '2023-01-15', 'Ban Quản Lý', 3000000.00, 'ACTIVE'),
(2, 'NV001', 'lananh', '123456', 'Trần Thị Lan Anh', '0912345678', 'lananh@gymfit.vn', 'STAFF', 'Ca Sáng (06:00 - 14:00)', 7500000.00, '2023-06-10', 'Bộ Phận Lễ Tân', 0.00, 'ACTIVE'),
(3, 'NV002', 'quocbao', '123456', 'Lê Quốc Bảo', '0923456789', 'quocbao@gymfit.vn', 'STAFF', 'Ca Chiều - Tối (14:00 - 22:00)', 7500000.00, '2023-09-01', 'Bộ Phận Lễ Tân', 0.00, 'ACTIVE');

-- 2. Gói tập mẫu
INSERT INTO `goi_tap` (`id`, `ma_goi`, `ten_goi`, `thoi_han_thang`, `gia_tien`, `mo_ta`, `quyen_loi`, `trang_thai`) VALUES
(1, 'GT01', 'Gói Trải Nghiệm 1 Tháng', 1, 450000.00, 'Phù hợp người mới bắt đầu tập thử, không ràng buộc lâu dài.', 'Toàn bộ khu vực tạ & cardio\nTủ đồ miễn phí\nPhòng tắm nóng lạnh', 'ACTIVE'),
(2, 'GT02', 'Gói Tiêu Chuẩn 3 Tháng', 3, 1200000.00, 'Tiết kiệm hơn, định hình thói quen tập luyện đều đặn.', 'Toàn quyền tập gym & cardio\nTủ đồ miễn phí\nĐo chỉ số InBody miễn phí 1 lần/tháng', 'ACTIVE'),
(3, 'GT03', 'Gói Nâng Cao 6 Tháng', 6, 2100000.00, 'Gói tập được yêu thích nhất, cam kết thay đổi vóc dáng.', 'Full quyền gym, cardio\nĐo InBody không giới hạn\nTặng 2 buổi hướng dẫn cùng HLV cá nhân', 'ACTIVE'),
(4, 'GT04', 'Gói VIP Toàn Diện 1 Năm', 12, 3600000.00, 'Tận hưởng trọn vẹn dịch vụ cao cấp nhất tại phòng tập.', 'Toàn quyền phòng gym, yoga, xông hơi\nKhăn tập & tủ đồ riêng\nTặng 5 buổi PT 1-1\nBảo lưu tối đa 30 ngày', 'ACTIVE'),
(5, 'GT05', 'Gói PT Kèm Riêng 1-1', 2, 6000000.00, '24 buổi tập chuyên sâu cùng Huấn Luyện Viên cá nhân.', 'HLV thiết kế giáo án riêng\nLộ trình dinh dưỡng theo mục tiêu\nTheo dõi sát sao từng buổi tập', 'ACTIVE');

-- 3. Hội viên mẫu (ngày hết hạn tự tính động)
INSERT INTO `hoi_vien` (`id`, `ma_hv`, `ho_ten`, `so_dien_thoai`, `email`, `gioi_tinh`, `ngay_tham_gia`, `goi_tap_id`, `ngay_het_han`, `trang_thai`, `avatar`) VALUES
(1, 'HV1001', 'Phạm Đức Anh', '0987654321', 'ducanh@gmail.com', 'Nam', '2024-01-10', 4, DATE_ADD(CURRENT_DATE(), INTERVAL 180 DAY), 'ACTIVE', 'https://api.dicebear.com/7.x/initials/svg?seed=Pham+Duc+Anh'),
(2, 'HV1002', 'Vũ Hải Yến', '0978123456', 'haiyen.vu@gmail.com', 'Nữ', '2024-02-15', 3, DATE_ADD(CURRENT_DATE(), INTERVAL 45 DAY), 'ACTIVE', 'https://api.dicebear.com/7.x/initials/svg?seed=Vu+Hai+Yen'),
(3, 'HV1003', 'Đỗ Tuấn Kiệt', '0965333444', 'tuankiet@yahoo.com', 'Nam', '2024-03-01', 1, DATE_ADD(CURRENT_DATE(), INTERVAL 3 DAY), 'ACTIVE', 'https://api.dicebear.com/7.x/initials/svg?seed=Do+Tuan+Kiet'),
(4, 'HV1004', 'Ngô Thu Thảo', '0943999888', 'thuthao.ngo@gmail.com', 'Nữ', '2023-11-20', 2, DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY), 'EXPIRED', 'https://api.dicebear.com/7.x/initials/svg?seed=Ngo+Thu+Thao'),
(5, 'HV1005', 'Trương Gia Huy', '0918777666', 'giahuy@outlook.com', 'Nam', '2024-04-01', NULL, NULL, 'PENDING', 'https://api.dicebear.com/7.x/initials/svg?seed=Truong+Gia+Huy');

-- 4. Hóa đơn mẫu
INSERT INTO `hoa_don` (`id`, `ma_hd`, `hoi_vien_id`, `goi_tap_id`, `so_tien`, `phuong_thuc`, `nhan_vien_id`, `ghi_chu`, `created_at`) VALUES
(1, 'HD892301', 1, 4, 3600000.00, 'CHUYEN_KHOAN', 2, 'Thanh toán qua quét mã QR ngân hàng', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 'HD892302', 2, 3, 2100000.00, 'THE', 3, 'Quẹt thẻ máy POS', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 'HD892303', 3, 1, 450000.00, 'TIEN_MAT', 2, 'Thu tiền mặt tại quầy lễ tân', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- 5. Check-in mẫu
INSERT INTO `check_in` (`id`, `hoi_vien_id`, `nhan_vien_id`, `thoi_gian_check_in`, `hop_le`, `ghi_chu`) VALUES
(1, 1, 2, DATE_SUB(NOW(), INTERVAL 2 HOUR), 1, 'Check-in thành công. Gói VIP còn 180 ngày.'),
(2, 2, 2, DATE_SUB(NOW(), INTERVAL 4 HOUR), 1, 'Check-in thành công. Gói 6 tháng còn 45 ngày.');
